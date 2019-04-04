<?php

namespace Hametuha\ForYourEyesOnly;

use Hametuha\ForYourEyesOnly\Pattern\Singleton;

/**
 * Capability controller.
 *
 * @package fyeo
 */
class Capability extends Pattern\Singleton {

	/**
	 * Get capability list.
	 *
	 * @return array
	 */
	public function capabilities_list() {
		return apply_filters( 'fyeo_capabilities_list', [
			'subscriber'    => __( 'Subscriber', 'fyeo' ),
			'contributor'   => __( 'Contributor', 'fyeo' ),
			'author'        => __( 'Author', 'fyeo' ),
			'editor'        => __( 'Editor', 'fyeo' ),
			'administrator' => __( 'Administrator', 'fyeo' ),
		] );
	}

	/**
	 * Detect if user has capability.
	 *
	 * @param string $capability
	 * @param null|int $user_id
	 * @return bool
	 */
	public function has_capability( $capability, $user_id = null ) {
		if ( is_null( $user_id ) ) {
			$user_id = get_current_user_id();
		}
		if ( ! $user_id || ! ( $user = get_userdata( $user_id ) ) ) {
			return false;
		}
		$capability = $this->map_old_cap( $capability );
		if ( $user->has_cap( $capability ) ) {
			return true;
		} else {
			return apply_filters( 'fyeo_user_has_cap', false, $user );
		}
	}

	/**
	 * Mapping old capability to new one.
	 *
	 * @param string $cap
	 * @return string
	 */
	protected function map_old_cap( $cap ) {
		switch ( $cap ) {
			case 'reader':
				return 'subscriber';
			case 'writer':
				return 'author';
			default:
				return $cap;
		}
	}
}
