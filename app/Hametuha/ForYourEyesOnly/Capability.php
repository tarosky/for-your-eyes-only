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
			'read'              => __( 'Subscriber', 'fyeo' ),
			'edit_posts'        => __( 'Contributor', 'fyeo' ),
			'publish_posts'     => __( 'Author', 'fyeo' ),
			'edit_others_posts' => __( 'Editor', 'fyeo' ),
			'manage_options'    => __( 'Administrator', 'fyeo' ),
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
				return 'read';
			case 'writer':
				return 'edit_post';
			default:
				return $cap;
		}
	}
}
