<?php
/**
 * Class ParserTest
 *
 * @package For_Your_Eyes_Only
 */

/**
 * Tests for Hametuha\ForYourEyesOnly\Parser::render().
 */
class ParserTest extends WP_UnitTestCase {

	/**
	 * @var \Hametuha\ForYourEyesOnly\Parser
	 */
	private $parser = null;

	public function setUp(): void {
		parent::setUp();
		$this->parser = \Hametuha\ForYourEyesOnly\Parser::get_instance();
	}

	public function tearDown(): void {
		remove_all_filters( 'fyeo_default_render_style' );
		parent::tearDown();
	}

	/**
	 * block.json（v1.2.0以降）は "dynamic" 属性に静的な初期値 "" を持つため、
	 * render_callback には常に $attributes['dynamic'] === '' が渡り得る。
	 * shortcode_atts() はキーが既に存在する値を上書きしないため、
	 * 'fyeo_default_render_style' フィルターで独自のデフォルトを設定しているサイトでは
	 * そのフィルターが無視されてしまう不具合があった。
	 */
	public function test_dynamic_attribute_respects_filter_even_when_explicitly_empty() {
		add_filter( 'fyeo_default_render_style', function () {
			return 'dynamic';
		} );

		$user_id = self::factory()->user->create( [ 'role' => 'subscriber' ] );
		wp_set_current_user( $user_id );

		// block.json由来で dynamic 属性が明示的に空文字として渡ってくるケースを再現する。
		$output = $this->parser->render(
			[
				'dynamic'    => '',
				'capability' => 'read',
			],
			'secret content'
		);

		// 'dynamic' モードとして扱われ、権限があるユーザーには本文がそのまま返る。
		$this->assertSame( 'secret content', $output );
	}

	/**
	 * フィルターを登録していないサイトでは、従来通り非同期（Async）モードのままであることを確認する。
	 */
	public function test_dynamic_attribute_defaults_to_async_without_filter() {
		$output = $this->parser->render(
			[
				'dynamic'    => '',
				'capability' => 'read',
			],
			'secret content'
		);

		$this->assertStringContainsString( 'data-post-id', $output );
	}
}
