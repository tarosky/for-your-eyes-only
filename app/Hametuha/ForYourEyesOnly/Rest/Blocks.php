<?php

namespace Hametuha\ForYourEyesOnly\Rest;


use Aws\CloudFront\Exception\Exception;
use Hametuha\ForYourEyesOnly\Pattern\RestApi;

/**
 * REST API for blocks
 *
 * @package Hametuha\ForYourEyesOnly\Rest
 */
class Blocks extends RestApi {

	protected function route() {
		return 'blocks/(?P<post_id>\d+)';
	}

	protected function get_params( $http_method ) {
		return [
			'post_id' => [
				'required'    => true,
				'type'        => 'integer',
				'description' => __( 'Post ID', 'fyeo' ),
				'validate_callback' => [ $this, 'is_numeric' ],
			],
		];
	}

	/**
	 * Handle GET request.
	 *
	 * @param \WP_REST_Request $request
	 * @throws Exception
	 * @return array
	 */
	protected function handle_get( \WP_REST_Request $request ) {
		$post = get_post( $request->get_param( 'post_id' ) );
		if ( ! $post ) {
			throw new \Exception( __( 'Post not found.', 'fyeo' ), 404 );
		}
		// Check capability.
		if ( 'publish' !== $post->post_status && ! current_user_can( 'edit_post', $post->ID ) ) {
			if ( ! apply_filters( 'fyeo_can_display_non_public', false, $post ) ) {
				throw new \Exception( __( 'You have no capability.', 'fyeo' ), 403 );
			}
		}
		// Change flag for parsing.
		$this->parser->set_skip_frag( true );
		$blocks = $this->parser->parse( $post, get_current_user_id() );
		return $blocks;
	}
}
