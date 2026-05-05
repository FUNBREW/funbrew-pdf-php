# FUNBREW PDF PHP SDK

Official PHP client library for the [FUNBREW PDF API](https://pdf.funbrew.cloud).

[日本語ドキュメント](README.ja.md)

## Installation

This package is distributed via GitHub (not Packagist). Add the repository to your project, then require the package:

```bash
composer config repositories.funbrew-pdf-php vcs https://github.com/FUNBREW/funbrew-pdf-php
composer require funbrew/pdf-php:^1.2
```

## Quick Start

```php
use Funbrew\Pdf\FunbrewPdf;

$pdf = new FunbrewPdf('sk-your-api-key');

// HTML to PDF
$result = $pdf->fromHtml('<h1>Hello World</h1>');
echo $result['data']['download_url'];

// URL to PDF
$result = $pdf->fromUrl('https://example.com');

// Markdown to PDF
$result = $pdf->fromMarkdown('# Hello World', 'modern');

// List available Markdown themes
$themes = $pdf->markdownThemes();

// Template to PDF
$result = $pdf->fromTemplate('invoice', [
    'company_name' => 'Acme Inc.',
    'amount' => '1,000',
]);
```

## Features

```php
// Generate PDF and send via email
$result = $pdf->fromHtmlWithEmail(
    '<h1>Invoice</h1>',
    'customer@example.com',
    'Your invoice is ready',
);

// Test mode (no count, TEST watermark)
$result = $pdf->test('<h1>Test</h1>');

// File operations
$info = $pdf->info('uuid.pdf');
$content = $pdf->download('uuid.pdf');
file_put_contents('output.pdf', $content);
$pdf->delete('uuid.pdf');

// Usage stats
$usage = $pdf->usage();
```

## Extract Text

```php
$result = $pdf->extractText('uuid.pdf');
echo $result['data']['text'];

// Per-page text
$result = $pdf->extractText('uuid.pdf', ['pages' => '1,3', 'per_page' => true]);
```

## Metadata

```php
// Read metadata
$meta = $pdf->metadata('uuid.pdf');
echo $meta['data']['title'];

// Set metadata
$result = $pdf->metadata('uuid.pdf', ['title' => 'Invoice', 'author' => 'FUNBREW']);
```

## Page Numbers

```php
$result = $pdf->pageNumbers('uuid.pdf', ['position' => 'top-right', 'format' => 'Page {page} of {total}']);
```

## PDF/A Conversion

```php
$result = $pdf->toPdfA('uuid.pdf');          // PDF/A-2b (default)
$result = $pdf->toPdfA('uuid.pdf', '1b');    // PDF/A-1b
```

## PDF to Image

```php
// Convert all pages to PNG
$result = $pdf->toImage('uuid.pdf');

// Convert to JPG with custom DPI
$result = $pdf->toImage('uuid.pdf', 'jpg', ['pages' => '1,3', 'dpi' => 300]);
```

## Markdown Preview

```php
$preview = $pdf->markdownPreview('# Hello', 'modern');
echo $preview['data']['html'];
```

## SaaS Management API

Templates, webhooks, and storage configuration management (SaaS edition only).

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

// External storage configuration
$pdf->storageConfig();
$pdf->createStorageConfig('s3', ['bucket' => 'my-bucket', 'region' => 'us-east-1']);
$pdf->updateStorageConfig(['is_active' => false]);
$pdf->deleteStorageConfig();
```

## Split / Rotate / Compress

```php
// Split PDF (extract pages)
$result = $pdf->split('uuid.pdf', '1-3');
$result = $pdf->split('uuid.pdf', '1,3,5');

// Rotate PDF pages (90, 180, 270)
$result = $pdf->rotate('uuid.pdf', 90);
$result = $pdf->rotate('uuid.pdf', 180, '1,3'); // specific pages

// Compress PDF (low, medium, high)
$result = $pdf->compress('uuid.pdf');
echo $result['data']['savings_percent'] . '%';
$result = $pdf->compress('uuid.pdf', 'low');
```

## Options

```php
$result = $pdf->fromHtml('<h1>Hello</h1>', [
    'options' => ['page-size' => 'A3', 'orientation' => 'Landscape'],
    'expiration_hours' => 168,
    'max_downloads' => 5,
    'password' => 'secret',
    'watermark' => 'CONFIDENTIAL',
]);
```

## Error Handling

```php
use Funbrew\Pdf\FunbrewException;

try {
    $result = $pdf->fromHtml('<h1>Hello</h1>');
} catch (FunbrewException $e) {
    echo $e->getMessage();  // Error message
    echo $e->getCode();     // HTTP status code
}
```

## License

MIT
