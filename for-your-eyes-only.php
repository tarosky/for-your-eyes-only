<?php
/*
Plugin Name: For Your Eyes Only
Plugin URI: https://wordpress.org/plugins/for-your-eyes-only
Description: A block restricted only for specified users.
Author: Tarosky INC.
Author URI: https://tarosky.co.jp
Version: nightly
Requires at least: 6.6
Requires PHP: 7.4
Text Domain: fyeo
Domain Path: /languages/
*/

defined( 'ABSPATH' ) || die();

add_action( 'plugins_loaded', function () {
	// Load translations.
	load_plugin_textdomain( 'fyeo', false, basename( __DIR__ ) . '/languages' );
	// Version
	$info = get_file_data( __FILE__, [
		'version' => 'Version',
	] );
	// Load autoloader.
	require __DIR__ . '/vendor/autoload.php';
	\Hametuha\ForYourEyesOnly::get_instance()->set_version( $info['version'] );
} );
