<?php
/**
 * Class SampleTest
 *
 * @package For_Your_Eyes_Only
 */

/**
 * Sample test case.
 */
class DirTest extends WP_UnitTestCase {

	/**
	 * @var \Hametuha\ForYourEyesOnly
	 */
	private $bootstrap = null;

	public function setUp() {
		$this->bootstrap = \Hametuha\ForYourEyesOnly::get_instance();
	}


	/**
	 * A single example test.
	 */
	public function test_sample() {
		$this->assertEquals( 'for-your-eyes-only', basename( $this->bootstrap->dir ) );
		$this->assertTrue( is_numeric( preg_match( '#$https?://(.*)/wp-content/plugins/for-your-eyes-only$#u', $this->bootstrap->url ) ) );
	}
}
