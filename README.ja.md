# FUNBREW PDF PHP SDK

FUNBREW PDF APIのPHPクライアントライブラリです。

## インストール

```bash
composer require funbrew/pdf-php
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

## オプション

```php
$result = $pdf->fromHtml('<h1>Hello</h1>', [
    'options' => ['page-size' => 'A3'],
    'expiration_hours' => 168,
    'max_downloads' => 5,
    'password' => 'secret',
    'watermark' => 'CONFIDENTIAL',
]);
```
