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
		add_action( 'init',[ $this, 'register_block_types' ], 11 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_style' ] );
		// Register route.
		Blocks::get_instance();
		// Register command if exists.
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			\WP_CLI::add_command( ForYourEyesOnly\Commands\i18n::COMMAND_NAME, ForYourEyesOnly\Commands\i18n::class );
		}
	}

	/**
	 * Register all assets.
	 */
	public function register_assets() {
		$locale = get_locale();
		foreach ( [
			[ 'js', 'block', [ 'wp-blocks', 'wp-i18n', 'wp-editor', 'wp-components' ], true ],
		    [ 'js', 'block-renderer', $this->add_plugin_deps( [ 'wp-i18n', 'jquery', 'wp-api-fetch' ] ), true ],
			[ 'css', 'block', [ 'dashicons' ], true ],
			[ 'css', 'theme', [], true ],
	  	] as list ( $type, $name, $deps, $footer ) ) {
			$handle = implode( '-', [ 'fyeo', $name, $type ] );
			$url = $this->url . "/assets/{$type}/{$name}.{$type}";
			switch ( $type ) {
				case 'js':
					wp_register_script( $handle, $url, $deps, $this->version, $footer );
					$handle_file = sprintf( '%s/languages/fyeo-%s-%s.json', $this->dir, $locale, $handle );
					if ( file_exists( $handle_file ) ) {
						wp_set_script_translations( $handle, 'fyeo', $this->dir . '/languages' );
					}
					break;
				case 'css':
					wp_register_style( $handle, $url, $deps, $this->version );
					break;
			}
		}
		wp_localize_script( 'fyeo-block-js', 'FyeoBlockVars', [
			'capabilities'  => $this->capability->capabilities_list(),
			'placeholder'   => $this->parser->tag_line(),
		] );
		wp_localize_script( 'fyeo-block-renderer-js', 'FyeoBlockRenderer', [
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
		register_block_type( 'fyeo/block', [
			'editor_script' => 'fyeo-block-js',
			'editor_style'  => 'fyeo-block-css',
			'render_callback' => [ $this->parser, 'render' ],
		] );
	}

	/**
	 * Enqueue front end style.
	 */
	public function enqueue_style() {
		if ( apply_filters( 'fyeo_enqueue_style', true ) ) {
			wp_enqueue_style( 'fyeo-theme-css' );
		}
	}
}
