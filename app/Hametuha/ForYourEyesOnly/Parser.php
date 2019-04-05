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
	 * @param bool $bool
	 */
	public function set_skip_frag( $bool ) {
		$this->skip_flag = (bool) $bool;
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
		$post = get_post( $post );
		setup_postdata( $post );
		$post_content = sprintf( '<root>%s</root>', apply_filters( 'the_content', $post->post_content ) );
		wp_reset_postdata();
		// Parse dom content.
		$html5 = new HTML5();
		$dom = $html5->loadHTML( $post_content );
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
			return sprintf( "<div class=\"fyeo-content-valid\" data-capability=\"%s\">\n%s\n</div>", esc_attr( $attributes['capability'] ), $content );
		}
		static $count = 0;
		$attributes = shortcode_atts( [
			'tag_line' => $this->tag_line(),
			'capability' => 'subscriber',
		], $attributes, 'fyeo' );
		if ( false !== strpos( $attributes['tag_line'], '%s' ) ) {
			$attributes['tag_line'] = sprintf( $attributes['tag_line'], $this->login_url() );
		}
		if ( ! $count ) {
			add_action( 'wp_footer', [ $this, 'enqueue_renderer' ], 1 );
		}
		$count++;
		return sprintf(
			"<div data-post-id=\"%d\" class=\"fyeo-content\" style=\"position: relative;\">\n%s\n</div>",
			get_the_ID(),
			wp_kses_post( wpautop( trim( $attributes['tag_line'] ) ) )
		);
	}

	/**
	 * Get tag line
	 *
	 * @return string
	 */
	public function tag_line() {
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

	/**
	 * Enqueue front end script.
	 */
	public function enqueue_renderer() {
		wp_enqueue_script( 'fyeo-block-renderer-js' );
	}

}
