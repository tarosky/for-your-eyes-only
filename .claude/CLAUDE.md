# For Your Eyes Only

WordPress用の制限ブロックプラグイン。指定した権限を持つユーザーにのみコンテンツを表示する。

## プロジェクト概要

- **リポジトリ**: tarosky/for-your-eyes-only
- **WordPress.org**: https://wordpress.org/plugins/for-your-eyes-only/
- **PHP**: 7.4以上
- **テキストドメイン**: `fyeo`

## ディレクトリ構成

```
├── app/Hametuha/ForYourEyesOnly/    # PHPクラス
│   ├── ForYourEyesOnly.php          # ブートストラップ
│   ├── Parser.php                    # ブロックレンダリング
│   ├── Capability.php                # 権限管理
│   └── Rest/Blocks.php               # REST API
├── src/blocks/restricted-block/      # ブロックソース
│   ├── block.json                    # ブロック定義
│   ├── index.js                      # エディタースクリプト
│   ├── view.js                       # フロントエンドスクリプト
│   ├── render.php                    # サーバーサイドレンダリング
│   └── editor.scss                   # エディタースタイル
├── build/                            # ビルド成果物（gitignore）
├── languages/                        # 翻訳ファイル
└── .github/workflows/                # CI/CD
```

## 開発コマンド

### npm スクリプト

```bash
npm run start      # wp-env 開発環境起動
npm run build      # ブロックとCSSのビルド
npm run lint:js    # ESLint
npm run lint:css   # Stylelint
```

### Composer スクリプト

```bash
composer test      # PHPUnit
composer lint      # PHPCS
composer fix       # PHPCBF

# 翻訳
composer i18n:update   # POT生成 + PO更新
composer i18n          # MO + JSON生成（デプロイ時も実行）
```

## 翻訳ワークフロー

1. `composer i18n:update` でPOT/POを更新
2. `languages/fyeo-ja.po` の未翻訳文字列を翻訳
3. `composer i18n` でMO/JSONを生成

または `/i18n` カスタムコマンドで自動化。

## ブロックの仕組み

### レンダリング方式

1. **動的（Dynamic/PHP）**: サーバーサイドで権限チェック、キャッシュと相性が悪い
2. **非同期（Asynchronous）**: REST API経由でJSがコンテンツ取得、キャッシュ環境向け

### REST API

- エンドポイント: `fyeo/v1/blocks/{post_id}`
- 投稿内の制限ブロックコンテンツを配列で返す
- 権限がない場合は空配列 `[]` を返す

## 注意点

- `Parser::render()` で `skip_flag` が true の場合、デフォルト権限を適用する必要がある
- JSONの翻訳ファイルはスクリプトパスのmd5ハッシュで命名される
- `wp_set_script_translations()` はブロック登録後にハンドルを取得して呼び出す

## GitHub Actions

- **test.yml**: プルリクエスト時にPHPUnit実行
- **deploy.yml**: タグプッシュでWordPress.orgへデプロイ
- **release-drafter.yml**: リリースノート自動生成
- **release-publish.yml**: リリース公開時の処理
