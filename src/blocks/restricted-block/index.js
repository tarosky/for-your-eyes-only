/**
 * Restricted Block
 *
 * This block will be displayed only for specified users.
 */

import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InnerBlocks, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, RadioControl, TextareaControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

import metadata from './block.json';
import './editor.scss';

/**
 * Get capability options from localized data.
 *
 * @return {Array} Options array for SelectControl.
 */
function getCapabilityOptions() {
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
	return { options, defaultLabel };
}

const { options, defaultLabel } = getCapabilityOptions();

/**
 * Edit component for the Restricted Block.
 *
 * @param {Object}   props               Block props.
 * @param {Object}   props.attributes    Block attributes.
 * @param {Function} props.setAttributes Function to set attributes.
 * @return {JSX.Element} Block edit component.
 */
function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps();

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Visibility Setting', 'fyeo' ) }
					icon="admin-users"
					initialOpen={ true }
				>
					<SelectControl
						label={ __( 'Capability', 'fyeo' ) }
						value={ attributes.capability || FyeoBlockVars.default }
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
			<div { ...blockProps }>
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
		</>
	);
}

/**
 * Save component for the Restricted Block.
 *
 * @return {JSX.Element} Block save component.
 */
function Save() {
	return <InnerBlocks.Content />;
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: Save,
} );
