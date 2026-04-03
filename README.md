# FUNBREW PDF PHP SDK

Official PHP client library for the [FUNBREW PDF API](https://pdf.funbrew.cloud).

[日本語ドキュメント](README.ja.md)

## Installation

```bash
composer require funbrew/pdf-php
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

## Options

```php
$result = $pdf->fromHtml('<h1>Hello</h1>', [
    'options' => ['page-size' => 'A3'],
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
