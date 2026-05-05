# FUNBREW PDF PHP SDK

FUNBREW PDF APIのPHPクライアントライブラリです。

## インストール

このパッケージは Packagist ではなく GitHub から直接配布しています。`composer.json` にリポジトリを追加してインストールしてください。

```bash
composer config repositories.funbrew-pdf-php vcs https://github.com/FUNBREW/funbrew-pdf-php
composer require funbrew/pdf-php:^1.2
```

## 使い方

```php
use Funbrew\Pdf\FunbrewPdf;

$pdf = new FunbrewPdf('sk-your-api-key');

// HTML → PDF
$result = $pdf->fromHtml('<h1>Hello World</h1>');
echo $result['data']['download_url'];

// URL → PDF
$result = $pdf->fromUrl('https://example.com');

// Markdown → PDF
$result = $pdf->fromMarkdown('# Hello World', 'modern');

// Markdownテーマ一覧を取得
$themes = $pdf->markdownThemes();

// テンプレート → PDF
$result = $pdf->fromTemplate('invoice', [
    'company_name' => 'FUNBREW Inc.',
    'amount' => '100,000',
]);

// PDF生成 + メール送信
$result = $pdf->fromHtmlWithEmail(
    '<h1>請求書</h1>',
    'customer@example.com',
    '請求書をお送りします',
);

// テストモード（カウント除外 + TEST透かし）
$result = $pdf->test('<h1>Test</h1>');

// ファイル情報
$info = $pdf->info('uuid.pdf');

// ダウンロード
$content = $pdf->download('uuid.pdf');
file_put_contents('output.pdf', $content);

// 削除
$pdf->delete('uuid.pdf');

// 利用状況
$usage = $pdf->usage();
```

## Markdownプレビュー

```php
$preview = $pdf->markdownPreview('# Hello', 'modern');
echo $preview['data']['html'];
```

## SaaS管理API

テンプレート・Webhook・外部ストレージ設定の管理（SaaSエディションのみ）。

```php
// Templates
$pdf->templates();
$pdf->createTemplate('Invoice', 'invoice', '<h1>{{ name }}</h1>', [
    ['name' => 'name', 'required' => true],
]);
$pdf->updateTemplate(42, ['html_content' => '<h1>Updated</h1>']);
$pdf->deleteTemplate(42);

// Webhooks
$pdf->webhooks();
$pdf->createWebhook('https://example.com/hook', ['pdf.generated']);
$pdf->updateWebhook(7, ['is_active' => false]);
$pdf->deleteWebhook(7);

// 外部ストレージ設定
$pdf->storageConfig();
$pdf->createStorageConfig('s3', ['bucket' => 'my-bucket', 'region' => 'us-east-1']);
$pdf->updateStorageConfig(['is_active' => false]);
$pdf->deleteStorageConfig();
```

## テキスト抽出

```php
$result = $pdf->extractText('uuid.pdf');
echo $result['data']['text'];

// ページごとに取得
$result = $pdf->extractText('uuid.pdf', ['pages' => '1,3', 'per_page' => true]);
```

## メタデータ

```php
// 読み取り
$meta = $pdf->metadata('uuid.pdf');
echo $meta['data']['title'];

// 設定
$result = $pdf->metadata('uuid.pdf', ['title' => '請求書', 'author' => 'FUNBREW']);
```

## ページ番号挿入

```php
$result = $pdf->pageNumbers('uuid.pdf', ['position' => 'top-right', 'format' => 'Page {page} of {total}']);
```

## PDF/A変換

```php
$result = $pdf->toPdfA('uuid.pdf');          // PDF/A-2b（デフォルト）
$result = $pdf->toPdfA('uuid.pdf', '1b');    // PDF/A-1b
```

## PDF→画像変換

```php
// 全ページをPNGに変換
$result = $pdf->toImage('uuid.pdf');

// JPGに変換（カスタムDPI）
$result = $pdf->toImage('uuid.pdf', 'jpg', ['pages' => '1,3', 'dpi' => 300]);
```

## PDF分割 / 回転 / 圧縮

```php
// PDF分割（ページ抽出）
$result = $pdf->split('uuid.pdf', '1-3');
$result = $pdf->split('uuid.pdf', '1,3,5');

// ページ回転（90, 180, 270）
$result = $pdf->rotate('uuid.pdf', 90);
$result = $pdf->rotate('uuid.pdf', 180, '1,3'); // 特定ページのみ

// PDF圧縮（low, medium, high）
$result = $pdf->compress('uuid.pdf');
echo $result['data']['savings_percent'] . '%';
$result = $pdf->compress('uuid.pdf', 'low');
```

## オプション

```php
$result = $pdf->fromHtml('<h1>Hello</h1>', [
    'options' => ['page-size' => 'A3', 'orientation' => 'Landscape'],
    'expiration_hours' => 168,
    'max_downloads' => 5,
    'password' => 'secret',
    'watermark' => 'CONFIDENTIAL',
]);
```
