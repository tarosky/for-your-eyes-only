<?php
/*
Plugin Name: For Your Eyes Only
Plugin URI: https://wordpress.org/plugins/for-your-eyes-only
Description: A block restricted only for specified users.
Author: Hametuha INC.
Author URI: https://hametuha.co.jp
Version: 1.0.1
Text Domain: fyeo
Domain Path: /languages/
*/

defined( 'ABSPATH' ) || die();

add_action( 'plugins_loaded', function() {
	// Load translations.
	load_plugin_textdomain( 'fyeo', false, basename( __DIR__ ) . '/languages' );
	// Load autoloader.
	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require __DIR__ . '/vendor/autoload.php';
		$info = get_file_data( __FILE__, [
			'version' => 'Version',
		] );
		\Hametuha\ForYourEyesOnly::get_instance()->set_version( $info['version'] );
	}
} );

