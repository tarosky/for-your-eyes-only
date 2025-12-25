<?php
/**
 * Render callback for the Restricted Block.
 *
 * This file is used by block.json's "render" property.
 *
 * @package fyeo
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#render
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

// Get the parser instance and render.
$fyeo = \Hametuha\ForYourEyesOnly::get_instance();
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $fyeo->parser->render( $attributes, $content );
