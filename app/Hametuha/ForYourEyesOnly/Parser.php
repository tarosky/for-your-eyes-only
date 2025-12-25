<?php

namespace Hametuha\ForYourEyesOnly;


use Hametuha\ForYourEyesOnly\Pattern\Singleton;
use Masterminds\HTML5;

/**
 * Contents parser
 *
 * @package fyeo
 */
class Parser extends Singleton {

	/**
	 * @var bool
	 */
	private $skip_flag = false;

	/**
	 * Set flag.
	 *
	 * @param bool $skip
	 */
	public function set_skip_frag( $skip ) {
		$this->skip_flag = (bool) $skip;
	}

	/**
	 * Parse contents.
	 *
	 * @param null|int|\WP_Post $post
	 * @param int|null          $user_id
	 * @return array
	 */
	public function parse( $post = null, $user_id = null ) {
		$blocks = [];
		$post   = get_post( $post );
		setup_postdata( $post );
		$post_content = sprintf( '<root>%s</root>', apply_filters( 'the_content', $post->post_content ) );
		wp_reset_postdata();
		// Parse dom content.
		$html5 = new HTML5();
		$dom   = $html5->loadHTML( $post_content );
		$xpath = new \DOMXPath( $dom );
		foreach ( $xpath->query( "//*[contains(@class, 'fyeo-content-valid')]" ) as $div ) {
			/* @var \DOMElement $div */
			$capability = $div->getAttribute( 'data-capability' );
			if ( ! $this->capability->has_capability( $capability, $user_id ) ) {
				$blocks[] = [];
			} else {
				$blocks[] = $dom->saveXML( $div );
			}
		}
		return $blocks;
	}

	/**
	 * Render callback
	 *
	 * @param string[] $attributes
	 * @param string   $content
	 * @return string
	 */
	public function render( $attributes = [], $content = '' ) {
		// If flag is on, returns full content.
		if ( $this->skip_flag ) {
			$capability = ! empty( $attributes['capability'] ) ? $attributes['capability'] : $this->capability->default_capability();
			return sprintf( "<div class=\"fyeo-content-valid\" data-capability=\"%s\">\n%s\n</div>", esc_attr( $capability ), $content );
		}
		static $count = 0;
		$attributes   = shortcode_atts( [
			'dynamic'    => apply_filters( 'fyeo_default_render_style', '' ),
			'tag_line'   => $this->tag_line(),
			'capability' => $this->capability->default_capability(),
		], $attributes, 'fyeo' );
		// Build tagline with URL.
		if ( false !== strpos( $attributes['tag_line'], '%s' ) ) {
			$attributes['tag_line'] = sprintf( $attributes['tag_line'], $this->login_url() );
		}
		++$count;
		switch ( $attributes['dynamic'] ) {
			case 'dynamic':
				// This is dynamic rendering.
				if ( $this->capability->has_capability( $attributes['capability'] ) ) {
					return $content;
				}
				return sprintf(
					"<div class=\"fyeo-content\">\n%s\n</div>",
					wp_kses_post( wpautop( trim( $attributes['tag_line'] ) ) )
				);
			default:
				// Async rendering.
				return sprintf(
					"<div data-post-id=\"%d\" class=\"fyeo-content\" style=\"position: relative;\">\n%s\n</div>",
					get_the_ID(),
					wp_kses_post( wpautop( trim( $attributes['tag_line'] ) ) )
				);
		}
	}

	/**
	 * Get tag line
	 *
	 * @return string
	 */
	public function tag_line() {
		// translators: %s is login URL.
		return (string) apply_filters( 'fyeo_tag_line', __( 'To see this section, please <a href="%s" rel="nofollow">log in</a>.', 'fyeo' ) );
	}

	/**
	 * Get redirect URL.
	 *
	 * @param null|int|\WP_Post $post
	 * @return string
	 */
	private function get_redirect_to( $post = null ) {
		$post = get_post( $post );

		/**
		 * fyeo_redirect_url
		 *
		 * Redirect URL to login.
		 *
		 * @param string   $permalink Default is post's permalink.
		 * @param \WP_Post $post      Post object.
		 */
		return (string) apply_filters( 'fyeo_redirect_url', get_permalink( $post ), $post );
	}

	/**
	 * Get login URL.
	 *
	 * @param null|int|\WP_Post $post
	 * @return string
	 */
	private function login_url( $post = null ) {
		$post        = get_post( $post );
		$redirect_to = $this->get_redirect_to( $post );
		/**
		 * fyeo_redirect_key
		 *
		 * Query parameter for Redirect URL.
		 *
		 * @param string $key Default is 'redirect_to'
		 */
		$key = apply_filters( 'fyeo_redirect_key', 'redirect_to' );
		/**
		 * fyeo_login_url
		 *
		 * Query parameter for Redirect URL.
		 *
		 * @param string $url Default is wp_login_url()
		 */
		$url = apply_filters( 'fyeo_login_url', wp_login_url() );
		if ( $redirect_to ) {
			$url = add_query_arg( [
				rawurlencode( $key ) => rawurlencode( $redirect_to ),
			], $url );
		}
		return $url;
	}
}
