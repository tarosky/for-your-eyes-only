/**
 * Restricted block
 *
 * @package fyeo
 */

const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const { Fragment } = wp.element;
const { InnerBlocks, InspectorControls } = wp.editor;
const { PanelBody, SelectControl } = wp.components;

/* global FyeoBlockVars:false */

const defaultLabel = __( 'Default(Subscriber)', 'fyeo' );
const options = [ {
  label: defaultLabel,
  value: '',
} ];
for ( let prop in FyeoBlockVars.capabilities ) {
  if ( FyeoBlockVars.capabilities.hasOwnProperty( prop ) ) {
    options.push( {
      value: prop,
      label: FyeoBlockVars.capabilities[ prop ],
    } );
  }
}


registerBlockType( 'fyeo/block', {

  title: __( 'Restricted Block', 'fyeo' ),

  icon: 'hidden',

  category: 'common',

  keywords: [ __( 'Restricted', 'fyeo' ), __( 'For Your Eyes Only', 'fyeo' ) ],

  description: __( 'This block will be displayed only for specified users.', 'fyeo' ),

  attributes: {
    tag_line: {
      type: 'string',
      default: '',
    },
    capability: {
      type: 'string',
      default: '',
    },
  },

  edit({attributes, className, setAttributes}){
    return (
      <Fragment>
        <InspectorControls>
          <PanelBody
            title={ __( 'Capability', 'fyeo' ) }
            icon="admin-users"
            initialOpen={true}
          >
            <SelectControl
              label='' value={attributes.capability}
              options={options} onChange={( value ) => { setAttributes({ capability: value }) }} />
            <p className='description'>
              { __( 'This block will be displayed only for users specified above.', 'fyeo' ) }
            </p>
          </PanelBody>
          <PanelBody
            title={ __( 'Instruction', 'fyeo' ) }
            icon="info"
            initialOpen={ false }
          >
            <textarea className='components-textarea-control__input' value={attributes.tag_line} rows={3}
                        placeholder={ 'e.g.' + FyeoBlockVars.placeholder} onChange={(e) => {
                setAttributes({
                  tag_line: e.target.value,
                });
              }}/>
            <p className='description'>
              {__('This instruction will be displayed to users who have no capability. %s will be replaced with login URL.', 'fyeo')}
            </p>
          </PanelBody>
        </InspectorControls>
        <div className={className}>
          <span className='wp-block-fyeo-block__label'>
            { attributes.capability ? options.filter( option => attributes.capability === option.value ).map( option => option.label ).join(' ') : defaultLabel }
          </span>
          <InnerBlocks/>
        </div>
      </Fragment>
    )
  },

  save({className}){
    return (
      <InnerBlocks.Content />
    )
  }

} );
