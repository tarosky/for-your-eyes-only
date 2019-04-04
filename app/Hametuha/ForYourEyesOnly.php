<?php

namespace Hametuha;


use Hametuha\ForYourEyesOnly\Pattern\Singleton;

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
	}

	/**
	 * Register all assets.
	 */
	public function register_assets() {
		foreach ( [
			[ 'js', 'block', [ 'wp-blocks', 'wp-i18n', 'wp-editor', 'wp-components' ], true ],
			[ 'css', 'block', [ 'dashicons' ], true ],
	  	] as list ( $type, $name, $deps, $footer ) ) {
			$handle = implode( '-', [ 'fyeo', $name, $type ] );
			$url = $this->url . "/assets/{$type}/{$name}.{$type}";
			switch ( $type ) {
				case 'js':
					wp_register_script( $handle, $url, $deps, $this->version, $footer );
					break;
				case 'css':
					wp_register_style( $handle, $url, $deps, $this->version );
					break;
			}
		}
		wp_localize_script( 'fyeo-block-js', 'FyeoBlockVars', [
			'capabilities' => $this->capability->capabilities_list(),
			'placeholder'  => $this->tag_line(),
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
			'render_callback' => [ $this, 'render' ],
		] );
	}

	/**
	 * Render callback
	 *
	 * @param string[] $attributes
	 * @param string   $content
	 * @return string
	 */
	public function render( $attributes = [], $content = '' ) {
		$attributes = shortcode_atts( [
			'tag_line' => $this->tag_line(),
			'capability' => 'subscriber',
		], $attributes, 'fyeo' );
		if ( false !== strpos( $attributes['tag_line'], '%s' ) ) {
			$attributes['tag_line'] = sprintf( $attributes['tag_line'], $this->login_url() );
		}
		return sprintf(
			"<div data-post-id=\"%d\" class=\"fyeo-content\">\n%s\n</div>",
			get_the_ID(),
			wp_kses_post( wpautop( trim( $attributes['tag_line'] ) ) )
		);
	}

	/**
	 * Get tagline
	 *
	 * @return string
	 */
	public function tag_line() {
		return __( 'To see this section, please <a href="%s" rel="nofollow">log in</a>.', 'fyeo' );
	}

	/**
	 * Get redirect URL.
	 *
	 * @param null|int|\WP_Post $post
	 * @return string
	 */
	private function get_redirect_to( $post = null ) {
		$post = get_post( $post );
		return (string) apply_filters( 'fyeo_redirect_url', get_permalink( $post ), $post );
	}

	/**
	 * Get login URL.
	 *
	 * @param null|int|\WP_Post $post
	 * @return string
	 */
	private function login_url( $post = null ) {
		$post = get_post( $post );
		$redirect_to = $this->get_redirect_to( $post );
		$key = apply_filters( 'fyeo_redirect_key', 'redirect_to' );
		$url = apply_filters( 'fyeo_login_url', wp_login_url() );
		if ( $redirect_to ) {
			$url = add_query_arg( [
				rawurlencode( $key ) => rawurlencode( $redirect_to ),
			], $url );
		}
		return $url;
	}
}
