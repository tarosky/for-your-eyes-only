<?php
/**
 * Live Preview のデモデータ。
 *
 * シナリオ: 架空の会員制ニュースレター「The Quiet Ledger」の記事。無料の導入部分のあとに
 * Restricted Block を 3 つ並べる（購読者向け本文／寄稿者向けメモ＋独自の案内文／管理者向け・PHP 描画）。
 * 着地はこの記事のブロックエディタ（ID を 1000 に固定）で、ブロックを選ぶとサイドバーに設定が出る。
 *
 * このファイルがマスター。編集したら次で blueprint.json に埋め込むこと:
 *   php <skill>/scripts/blueprint.php embed .
 */
require '/wordpress/wp-load.php';

// runPHP はログインユーザー無しで動く。権限チェックのある API（tax_input など）が黙って無視されるので、管理者として実行する。
wp_set_current_user( 1 );

update_option( 'blogname', 'The Quiet Ledger' );
update_option( 'blogdescription', 'A members-only newsletter on personal finance' );

/**
 * 段落ブロックを作る。
 *
 * @param string $html Inner HTML of the paragraph.
 * @return array
 */
$paragraph = function ( $html ) {
	return array(
		'blockName'    => 'core/paragraph',
		'attrs'        => array(),
		'innerBlocks'  => array(),
		'innerHTML'    => '<p>' . $html . '</p>',
		'innerContent' => array( '<p>' . $html . '</p>' ),
	);
};

/**
 * 見出しブロックを作る。
 *
 * @param string $text Heading text.
 * @return array
 */
$heading = function ( $text ) {
	$html = '<h2 class="wp-block-heading">' . esc_html( $text ) . '</h2>';
	return array(
		'blockName'    => 'core/heading',
		'attrs'        => array(),
		'innerBlocks'  => array(),
		'innerHTML'    => $html,
		'innerContent' => array( $html ),
	);
};

/**
 * Restricted Block を作る。保存形式は InnerBlocks の中身だけ（ラッパー無し）。
 *
 * @param array $attrs  Block attributes (capability, tag_line, dynamic).
 * @param array $inner  Inner blocks.
 * @return array
 */
$restricted = function ( $attrs, $inner ) {
	return array(
		'blockName'    => 'fyeo/block',
		'attrs'        => $attrs,
		'innerBlocks'  => $inner,
		'innerHTML'    => '',
		'innerContent' => array_fill( 0, count( $inner ), null ),
	);
};

$blocks = array(
	// 無料で読める導入部分。
	$paragraph( 'Welcome to this week\'s issue. The first part is free for everyone; the rest is split into sections that only certain readers can see.' ),
	$paragraph( '<strong>Try it:</strong> select a restricted block to see its settings in the sidebar &mdash; who can see it, how it is rendered, and the message shown to everyone else.' ),

	// 1. 購読者（ログインユーザー）向けの本文。非同期（JS + REST API）描画。
	$restricted(
		array( 'capability' => 'read' ),
		array(
			$heading( 'This week: building a six-month buffer' ),
			$paragraph( 'Start by listing fixed costs only. Most readers overestimate how much they need because they include discretionary spending in the target.' ),
			$paragraph( 'Then automate a transfer on payday so the buffer grows before you have a chance to spend it.' ),
		)
	),

	// 2. 寄稿者向けの編集メモ。権限の無い人には独自の案内文（%s はログイン URL）を表示する。
	$restricted(
		array(
			'capability' => 'edit_posts',
			'tag_line'   => 'Editorial notes are visible to contributors only. <a href="%s" rel="nofollow">Log in</a> with a contributor account to read them.',
		),
		array(
			$paragraph( '<em>Editor\'s note:</em> fact-check the savings-rate figures against the latest survey before next week\'s follow-up.' ),
		)
	),

	// 3. 管理者だけに見せる運営メモ。PHP で描画する（キャッシュの無いサイト向け）。
	$restricted(
		array(
			'capability' => 'manage_options',
			'dynamic'    => 'dynamic',
		),
		array(
			$paragraph( '<em>Admin only:</em> this issue goes out to 1,284 subscribers on Friday at 7:00 am.' ),
		)
	),
);

// 着地 URL を固定するため、ID を 1000 にする。
$post_id = wp_insert_post( array(
	'import_id'    => 1000,
	'post_type'    => 'post',
	'post_status'  => 'publish',
	'post_title'   => 'Issue #42: The Six-Month Buffer',
	'post_name'    => 'issue-42-six-month-buffer',
	// wp_insert_post() は wp_unslash() するので、属性 JSON のエスケープ（< など）が壊れないよう slash しておく。
	'post_content' => wp_slash( serialize_blocks( $blocks ) ),
) );

// ブロックエディタのウェルカムガイドを閉じておく。
$preferences                   = get_user_meta( 1, $GLOBALS['wpdb']->get_blog_prefix() . 'persisted_preferences', true );
$preferences                   = is_array( $preferences ) ? $preferences : array();
$preferences['core/edit-post'] = array_merge(
	isset( $preferences['core/edit-post'] ) ? (array) $preferences['core/edit-post'] : array(),
	array( 'welcomeGuide' => false )
);
$preferences['_modified']      = gmdate( 'c' );
update_user_meta( 1, $GLOBALS['wpdb']->get_blog_prefix() . 'persisted_preferences', $preferences );

// 不要な既定コンテンツを片付ける。
wp_delete_post( 1, true ); // Hello world!
wp_delete_post( 2, true ); // Sample Page
