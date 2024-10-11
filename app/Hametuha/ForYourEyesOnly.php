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
		$locale = get_locale();
		$json   = $this->dir . '/wp-dependencies.json';
		if ( ! file_exists( $json ) ) {
			return;
		}
		$deps = json_decode( file_get_contents( $json ), true );
		if ( ! $deps ) {
			return;
		}
		foreach ( $deps as $dep ) {
			if ( empty( $dep['handle'] ) ) {
				continue;
			}
			$url = $this->url . '/' . $dep['path'];
			switch ( $dep['ext'] ) {
				case 'js':
					wp_register_script( $dep['handle'], $url, $dep['deps'], $dep['hash'], [
						'in_footer' => $dep['footer'],
						'strategy'  => 'defer',
					] );
					if ( in_array( 'wp-i18n', $dep['deps'], true ) ) {
						wp_set_script_translations( $dep['handle'], 'fyeo', $this->dir . '/languages' );
					}
					break;
				case 'css':
					wp_register_style( $dep['handle'], $url, $dep['deps'], $dep['hash'], 'screen' );
					break;
			}
		}
		wp_localize_script( 'fyeo-block', 'FyeoBlockVars', [
			'capabilities' => $this->capability->capabilities_list(),
			'default'      => $this->capability->default_capability(),
			'placeholder'  => $this->parser->tag_line(),
		] );
		wp_localize_script( 'fyeo-block-renderer', 'FyeoBlockRenderer', [
			'cookieTasting' => $this->cookie_tasting_exists(),
		] );
	}

	/**
	 * Register blocks.
	 */
	public function register_block_types() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		$view_styles = [];
		if ( apply_filters( 'fyeo_enqueue_style', true ) ) {
			$view_styles[] = 'fyeo-theme';
		}
		register_block_type( 'fyeo/block', [
			'editor_script_handles' => [ 'fyeo-block' ],
			'view_script_handles'   => [ 'fyeo-block-renderer' ],
			'editor_style_handles'  => [ 'fyeo-block' ],
			'view_style_handles'    => $view_styles,
			'render_callback'       => [ $this->parser, 'render' ],
		] );
	}
}
