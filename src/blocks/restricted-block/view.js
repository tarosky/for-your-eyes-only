/**
 * Block Renderer for Frontend
 *
 * This script fetches restricted block content via REST API
 * for users who have the required capability.
 */

import apiFetch from '@wordpress/api-fetch';
import { sprintf, __ } from '@wordpress/i18n';

const $ = jQuery;

/**
 * Convert block content via REST API.
 *
 * @param {number} id Post ID.
 */
const convertBlock = ( id ) => {
	const $containers = $( `.fyeo-content[data-post-id=${ id }]` );
	$containers.addClass( 'fyeo-content-loading' );

	apiFetch( {
		path: `fyeo/v1/blocks/${ id }`,
	} )
		.then( ( response ) => {
			$containers.each( ( index, div ) => {
				if ( ! response[ index ].trim() ) {
					return;
				}
				const $block = $( response[ index ] );
				$( div ).replaceWith( $block );
				$block.trigger( 'fyeo.block.updated' );
			} );
		} )
		.catch( ( err ) => {
			if ( err.data && err.data.status ) {
				switch ( err.data.status ) {
					case 403:
					case 401:
						return;
				}
			}
			$containers
				.addClass( 'fyeo-content-error' )
				.prepend(
					sprintf(
						'<p class="fyeo-content-error-string">%s</p>',
						__( 'Failed authentication', 'fyeo' )
					)
				);
		} )
		.finally( () => {
			$containers.removeClass( 'fyeo-content-loading' );
		} );
};

$( document ).ready( function() {
	const ids = [];
	$( '.fyeo-content' ).each( ( index, div ) => {
		const id = parseInt( $( div ).attr( 'data-post-id' ), 10 );
		if ( -1 === ids.indexOf( id ) ) {
			ids.push( id );
		}
	} );
	if ( ! ids.length ) {
		return;
	}
	ids.forEach( ( id ) => {
		if ( FyeoBlockRenderer.cookieTasting ) {
			CookieTasting.testBefore()
				.then( ( response ) => {
					if ( response.login ) {
						convertBlock( id );
					}
				} )
				.catch( () => {
					// Do nothing.
				} );
		} else {
			convertBlock( id );
		}
	} );
} );
