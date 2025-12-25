<?php

namespace Hametuha;


use Hametuha\ForYourEyesOnly\Pattern\Singleton;
use Hametuha\ForYourEyesOnly\Rest\Blocks;

/**
 * Bootstrap class
 *
 * @package fyeo
 */
class ForYourEyesOnly extends Singleton {

	/**
	 * Executed inside constructor.
	 */
	protected function init() {
		add_action( 'init', [ $this, 'register_assets' ] );
		add_action( 'init', [ $this, 'register_block_types' ], 11 );
		// Register route.
		Blocks::get_instance();
	}

	/**
	 * Register all assets.
	 */
	public function register_assets() {
		// Register theme CSS from wp-dependencies.json.
		$json = $this->dir . '/wp-dependencies.json';
		if ( file_exists( $json ) ) {
			$deps = json_decode( file_get_contents( $json ), true );
			if ( $deps ) {
				foreach ( $deps as $dep ) {
					if ( empty( $dep['handle'] ) ) {
						continue;
					}
					$url = $this->url . '/' . $dep['path'];
					switch ( $dep['ext'] ) {
						case 'css':
							wp_register_style( $dep['handle'], $url, $dep['deps'], $dep['hash'], 'screen' );
							break;
					}
				}
			}
		}
	}

	/**
	 * Register blocks.
	 */
	public function register_block_types() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Register block from block.json.
		$block = register_block_type( $this->dir . '/build/blocks/restricted-block' );

		// Add localized script data to editor script.
		if ( $block && ! empty( $block->editor_script_handles ) ) {
			$handle = $block->editor_script_handles[0];
			wp_localize_script( $handle, 'FyeoBlockVars', [
				'capabilities' => $this->capability->capabilities_list(),
				'default'      => $this->capability->default_capability(),
				'dynamic'      => apply_filters( 'fyeo_default_render_style', '' ),
				'placeholder'  => $this->parser->tag_line(),
			] );
			wp_set_script_translations( $handle, 'fyeo', $this->dir . '/languages' );
		}

		// Add localized script data to view script.
		if ( $block && ! empty( $block->view_script_handles ) ) {
			$handle = $block->view_script_handles[0];
			wp_localize_script( $handle, 'FyeoBlockRenderer', [
				'cookieTasting' => $this->cookie_tasting_exists(),
			] );
		}

		// Enqueue theme style if enabled.
		if ( apply_filters( 'fyeo_enqueue_style', true ) ) {
			add_action( 'wp_enqueue_scripts', function () {
				if ( has_block( 'fyeo/block' ) ) {
					wp_enqueue_style( 'fyeo-theme' );
				}
			} );
		}
	}
}
