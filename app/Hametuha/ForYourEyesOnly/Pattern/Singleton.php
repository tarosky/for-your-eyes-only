<?php

namespace Hametuha\ForYourEyesOnly\Pattern;

use Hametuha\ForYourEyesOnly\Capability;
use Hametuha\ForYourEyesOnly\Parser;

/**
 * Singleton
 *
 * @property string     $dir
 * @property string     $url
 * @property Capability $capability
 * @property Parser     $parser
 * @package fyeo
 */
abstract class Singleton {

	/**
	 * @var string
	 */
	public $version = '';

	/**
	 * @var static[]
	 */
	private static $instances = [];

	/**
	 * Constructor
	 */
	final protected function __construct() {
		$this->init();
	}

	/**
	 * Executed inside constructor.
	 */
	protected function init() {
		// Do something.
	}

	/**
	 * Get instance.
	 *
	 * @return static
	 */
	public static function get_instance() {
		$class_name = get_called_class();
		if ( ! isset( self::$instances[ $class_name ] ) ) {
			self::$instances[ $class_name ] = new $class_name();
		}
		return self::$instances[ $class_name ];
	}

	/**
	 * Set current version.
	 *
	 * @param string $version
	 */
	public function set_version( $version ) {
		$this->version = $version;
	}

	/**
	 * Detect if cookie-tasting exists.
	 *
	 * @return bool
	 */
	public function cookie_tasting_exists() {
		return function_exists( 'cookie_tasting_version' );
	}

	/**
	 * Add plugin dependencies.
	 *
	 * @param array $deps
	 * @return array
	 */
	public function add_plugin_deps( $deps ) {
		if ( $this->cookie_tasting_exists() ) {
			$deps[] = 'cookie-tasting-heartbeat';
		}
		return $deps;
	}

	/**
	 * Getter
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function __get( $name ) {
		switch ( $name ) {
			case 'capability':
				return Capability::get_instance();
			case 'parser':
				return Parser::get_instance();
			case 'dir':
				return dirname( __DIR__, 4 );
			case 'url':
				return untrailingslashit( plugin_dir_url( $this->dir . '/assets' ) );
			default:
				return null;
		}
	}
}
