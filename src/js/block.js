/*!
 * Restricted block
 *
 * @handle fyeo-block
 * @deps wp-blocks, wp-i18n, wp-element, wp-block-editor, wp-components
 */

const { registerBlockType } = wp.blocks;
const { __ } = wp.i18n;
const { Fragment } = wp.element;
const { InnerBlocks, InspectorControls } = wp.blockEditor;
const { PanelBody, SelectControl, RadioControl, TextareaControl } = wp.components;

/* global FyeoBlockVars:false */

const options = [];
let defaultLabel = '';
for ( const prop in FyeoBlockVars.capabilities ) {
	if ( FyeoBlockVars.capabilities.hasOwnProperty( prop ) ) {
		let label = FyeoBlockVars.capabilities[ prop ];
		if ( prop === FyeoBlockVars.default ) {
			label += __( '(Default)', 'fyeo' );
			defaultLabel = label;
		}
		options.push( {
			value: prop,
			label,
		} );
	}
}

registerBlockType( 'fyeo/block', {

	title: __( 'Restricted Block', 'fyeo' ),

	icon: 'hidden',

	category: 'common',

	keywords: [ __( 'Restricted', 'fyeo' ), __( 'For Your Eyes Only', 'fyeo' ) ],

	description: __(
		'This block will be displayed only for specified users.',
		'fyeo'
	),

	attributes: {
		tag_line: {
			type: 'string',
			default: '',
		},
		capability: {
			type: 'string',
			default: '',
		},
		dynamic: {
			type: 'string',
			default: '',
		},
	},

	edit( { attributes, className, setAttributes } ) {
		return (
			<Fragment>
				<InspectorControls>
					<PanelBody
						title={ __( 'Visibility Setting', 'fyeo' ) }
						icon="admin-users"
						initialOpen={ true }
					>
						<SelectControl
							label={ __( 'Capability', 'fyeo' ) }
							value={ attributes.capability }
							options={ options }
							onChange={ ( value ) => {
								setAttributes( { capability: value } );
							} }
							help={ __( 'This block will be displayed only for users specified above.', 'fyeo' ) }
						/>
						<hr />
						<RadioControl
							label={ __( 'Rendering Style', 'fyeo' ) }
							selected={ attributes.dynamic }
							options={ [
								{
									label: __( 'Asynchronous(JavaScript + REST API)', 'fyeo' ),
									value: '',
								},
								{
									label: __( 'Dynamic(PHP)', 'fyeo' ),
									value: 'dynamic',
								},
							] }
							onChange={ ( dynamic ) => {
								setAttributes( { dynamic } );
							} }
							help={ __( 'If WordPress is under cache, Asynchronous is recommended.', 'fyeo' ) }
						/>
						<hr />
						<TextareaControl
							label={ __( 'Tagline', 'fyeo' ) }
							value={ attributes.tag_line }
							rows={ 5 }
							placeholder={ 'e.g.' + FyeoBlockVars.placeholder }
							onChange={ ( tagLine ) => setAttributes( { tag_line: tagLine } ) }
							help={ __( 'This instruction will be displayed to users who have no capability. %s will be replaced with login URL.', 'fyeo' ) }
						/>
					</PanelBody>
				</InspectorControls>
				<div className={ className }>
					<span className="wp-block-fyeo-block__label">
						{ attributes.capability
							? options
								.filter(
									( option ) =>
										attributes.capability ===
										option.value
								)
								.map( ( option ) => option.label )
								.join( ' ' )
							: defaultLabel }
					</span>
					<InnerBlocks />
				</div>
			</Fragment>
		);
	},

	save() {
		return <InnerBlocks.Content />;
	},
} );
