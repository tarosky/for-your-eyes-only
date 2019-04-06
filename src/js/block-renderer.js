/**
 * Description
 */

/* global FyeoBlockRenderer: false */

const { sprintf, __ } = wp.i18n;
const $ = jQuery;

const convertBlock = ( id ) => {
  // Fetch api.
  const $containers = $( `.fyeo-content[data-post-id=${id}]` );
  $containers.addClass( 'fyeo-content-loading' );

  wp.apiFetch({
    path: `fyeo/v1/blocks/${id}`,
  }).then( response => {
    $containers.each( ( index, div ) => {
      if ( ! response[index].trim() ) {
        // No block. Not replace.
        return;
      }
      const $block = $( response[index] );
      $( div ).replaceWith( $block );
      $block.trigger( 'fyeo.block.updated' );
    } );
  }).catch(err => {
    if ( err.data && err.data.status ) {
      switch ( err.data.status ) {
        case 403:
        case 401:
          return;
      }
    }
    $containers.addClass('fyeo-content-error').prepend( sprintf(
      '<p class="fyeo-content-error-string">%s</p>',
      __( 'Failed authentication', 'fyeo' )
    ) );
  }).finally(() => {
    $containers.removeClass('fyeo-content-loading');
  });
};


$(document).ready(function () {
  const ids = [];
  $('.fyeo-content').each((index, div) => {
    const id = parseInt($(div).attr('data-post-id'), 10);
    if (-1 === ids.indexOf(id)) {
      ids.push(id);
    }
  });
  if ( !ids.length) {
    return;
  }
  ids.map( id => {
    if ( FyeoBlockRenderer.cookieTasting ) {
      CookieTasting.testBefore().then( response => {
        if ( response.login ) {
          convertBlock( id );
        }
      } ).catch( err => {
        // Do nothing.
      });
    } else {
      convertBlock( id );
    }
  });
});

