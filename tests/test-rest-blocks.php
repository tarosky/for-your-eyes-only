<?php
/**
 * Class RestBlocksTest
 *
 * @package For_Your_Eyes_Only
 */

/**
 * Tests for the fyeo/v1/blocks/{post_id} REST endpoint.
 */
class RestBlocksTest extends WP_UnitTestCase {

	/**
	 * @var int
	 */
	private $post_id = 0;

	public function setUp(): void {
		parent::setUp();
		do_action( 'rest_api_init' );
		$blocks        = [];
		foreach ( [ 'read', 'edit_posts', 'manage_options' ] as $cap ) {
			$blocks[] = sprintf(
				'<!-- wp:fyeo/block {"capability":"%1$s","tag_line":"Need %1$s"} --><p>Secret for %1$s</p><!-- /wp:fyeo/block -->',
				$cap
			);
		}
		$this->post_id = self::factory()->post->create( [
			'post_status'  => 'publish',
			'post_content' => implode( "\n\n", $blocks ),
		] );
	}

	public function tearDown(): void {
		\Hametuha\ForYourEyesOnly\Parser::get_instance()->set_skip_frag( false );
		parent::tearDown();
	}

	/**
	 * Request blocks as a user with the given role.
	 *
	 * @param string $role
	 * @return array
	 */
	private function request_as( $role ) {
		wp_set_current_user( self::factory()->user->create( [ 'role' => $role ] ) );
		$request  = new WP_REST_Request( 'GET', '/fyeo/v1/blocks/' . $this->post_id );
		$response = rest_get_server()->dispatch( $request );
		$this->assertSame( 200, $response->get_status() );
		return $response->get_data();
	}

	/**
	 * Every entry must be a string. Users without capability get an empty string,
	 * not an empty array (which broke view.js with "trim is not a function").
	 */
	public function test_subscriber_gets_strings_only() {
		$data = $this->request_as( 'subscriber' );
		$this->assertCount( 3, $data );
		foreach ( $data as $entry ) {
			$this->assertIsString( $entry );
		}
		$this->assertStringContainsString( 'Secret for read', $data[0] );
		$this->assertSame( '', $data[1] );
		$this->assertSame( '', $data[2] );
	}

	/**
	 * Administrators get all blocks.
	 */
	public function test_admin_gets_all_blocks() {
		$data = $this->request_as( 'administrator' );
		$this->assertCount( 3, $data );
		$this->assertStringContainsString( 'Secret for read', $data[0] );
		$this->assertStringContainsString( 'Secret for edit_posts', $data[1] );
		$this->assertStringContainsString( 'Secret for manage_options', $data[2] );
	}
}
