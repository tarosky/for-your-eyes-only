# For Your Eyes Only

Contributors: tarosky, Takahashi_Fumiki, hametuha
Tags: membership, login, restrict
Tested up to: 6.9
Stable tag: nightly
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.txt

指定したユーザーにのみコンテンツを表示する制限ブロックを追加します。

## 説明

このプラグインはブロックエディターに新しいブロックを追加します。
このブロックは、現在のユーザーの権限に応じて表示内容を変更します。

* ブロックに必要な権限を設定できます。
* 現在のユーザーがブロックに必要な権限を持っている場合、コンテンツが表示されます。
* それ以外の場合、ブロックはログインリンクとして表示されます。
* このブロックはインナーブロックなので、内部に任意のブロックをネストできます。再利用ブロックに変換することで、生産性を向上させることができます。

ブロックの表示例についてはスクリーンショットをご覧ください。

このプラグインはREST APIを使用してブロックコンテンツを変換するため、キャッシュされたWordPressでも使用できます。
[CloudFront](https://aws.amazon.com/cloudfront/)や[Cloudflare](https://www.cloudflare.com/)などのCDNを使用している場合でも、このプラグインは各ユーザーに適切なコンテンツを表示します。

パフォーマンス向上のため、[Cookie Tasting](https://wordpress.org/plugins/cookie-tasting/)の使用を推奨します。
サーバーサイドスクリプトにアクセスする前にCOOKIE値をチェックすることで、REST APIを含むサーバーへのアクセスを減らすことができます。

### フック

#### 表示のカスタマイズ

- `fyeo_tag_line` - 権限を持たないユーザーに表示されるデフォルトのタグラインをカスタマイズします。`%s`はログインURLに置換されます。
- `fyeo_login_url` - ログインURLを置き換えます。デフォルトは`wp_login_url()`です。
- `fyeo_redirect_url` - ログイン後のリダイレクトURLをカスタマイズします。第二引数として投稿オブジェクトを受け取ります。
- `fyeo_redirect_key` - リダイレクト用のクエリパラメータキーを変更します。デフォルトは`redirect_to`です。
- `fyeo_enqueue_style` - デフォルトのテーマスタイルを読み込むかどうか。`false`を返すと無効になります。

#### 権限の制御

- `fyeo_capabilities_list` - ブロック設定に表示される利用可能な権限のリストをカスタマイズします。
- `fyeo_default_capability` - デフォルトの権限を変更します。デフォルトは`read`です。
- `fyeo_user_has_cap` - 権限チェックの結果を上書きします。`$has_cap`、`$capability`、`$user`を受け取ります。

#### レンダリング

- `fyeo_default_render_style` - デフォルトのレンダリングスタイルを設定します。PHPレンダリングの場合は`dynamic`を、非同期の場合は空文字列を返します。
- `fyeo_can_display_non_public` - 非公開投稿の制限コンテンツの表示を許可します。`$post`オブジェクトを受け取ります。

#### REST API

- `fyeo_minimum_rest_capability` - REST APIへのアクセスを制御します。アクセスを拒否するには`false`を返します。

## インストール

1. プラグインファイルを`/wp-content/plugins/for-your-eyes-only`ディレクトリにアップロードするか、WordPressのプラグイン画面から直接インストールしてください。
2. WordPressの「プラグイン」画面からプラグインを有効化してください。
3. ブロックエディターで**制限ブロック**という新しいブロックが使用可能になります。

## よくある質問

### 開発への参加方法

コードは[GitHub](https://github.com/tarosky/for-your-eyes-only)でホストしています。PRやイシューの作成はお気軽にどうぞ。

## スクリーンショット

1. 非メンバーへのアクセスを制限する新しいブロックが追加されます。

## 変更履歴

### 1.1.0

* 所有権が変更されました。このプラグインを引き継いでくださった @hametuha に感謝します。
* 動的モードを追加。PHPレンダリングとして動作します。

### 1.0.1

* 自動デプロイを追加。

### 1.0.0

* 初回リリース。
