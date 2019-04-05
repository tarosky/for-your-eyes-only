<?php

namespace Hametuha\ForYourEyesOnly\Pattern;


/**
 * REST API base.
 *
 * @package fyeo
 */
abstract class RestApi extends Singleton {

	protected $namespace = 'fyeo/v1';

	/**
	 * Get route path.
	 *
	 * @return string
	 */
	abstract protected function route();

	/**
	 * Register REST Endpoint.
	 */
	protected function init() {
		add_action( 'rest_api_init', [ $this, 'register_rest_route' ] );
	}

	/**
	 * Get request params.
	 *
	 * @param string $http_method
	 * @return array
	 */
	abstract protected function get_params( $http_method );

	/**
	 * Register REST endpoint.
	 */
	public function register_rest_route() {
		$arguments = [];
		foreach ( [ 'GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS' ] as $http_method ) {
			$argument = [];
			$method_name = $this->get_method_name( $http_method );
			if ( ! method_exists( $this, $method_name ) ) {
				continue;
			}
			$params = $this->get_params( $http_method );
			$argument = [
				'methods'  => $http_method,
				'args'     => $params,
				'callback' => [ $this, 'callback' ],
				'permission_callback' => [ $this, 'permission_callback' ],
			];
			$arguments[] = $argument;
		}
		if ( count( $arguments ) ) {
			register_rest_route( $this->namespace, $this->route(), $arguments );
		}
	}

	/**
	 * Get method name.
	 *
	 * @param string $http_method
	 * @return string
	 */
	private function get_method_name( $http_method ) {
		return sprintf( 'handle_%s', strtolower( $http_method ) );
	}

	/**
	 * @param \WP_REST_Request $request
	 * @return array|\WP_Error|\WP_REST_Response
	 */
	public function callback( $request ) {
		try {
			$method_name = $this->get_method_name( $request->get_method() );
			if ( ! method_exists( $this, $method_name ) ) {
				throw new \Exception( __( 'Invalid request. The specified endpoint does not exist.', 'fyeo' ), 404 );
			}
			$response = $this->{$method_name}( $request );
			if ( is_array( $response ) ) {
				return new \WP_REST_Response( $response );
			} else {
				return $response;
			}
		} catch ( \Exception $e ) {
			return new \WP_Error( 'fyeo_rest_api_error', $e->getMessage(), [
				'status' => $e->getCode(),
			] );
		}
	}

	/**
	 * Validation for is_numeric
	 *
	 * @param mixed $var
	 * @return bool
	 */
	public function is_numeric( $var ) {
		return is_numeric( $var );
	}

	/**
	 * Check if user is logged in.
	 *
	 * @param \WP_REST_Request $request
	 * @return bool
	 */
	public function permission_callback( $request ) {
		return apply_filters( 'fyeo_minimum_rest_capability', is_user_logged_in(), $request );
	}
}
