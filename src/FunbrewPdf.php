<?php

namespace Funbrew\Pdf;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class FunbrewPdf
{
    private Client $client;

    public function __construct(
        private string $apiKey,
        private string $baseUrl = 'https://pdf.funbrew.cloud',
    ) {
        $this->client = new Client([
            'base_uri' => rtrim($this->baseUrl, '/'),
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'timeout' => 60,
        ]);
    }

    /**
     * Generate PDF from HTML
     */
    public function fromHtml(string $html, array $options = []): array
    {
        return $this->post('/api/pdf/generate-from-html', array_merge(
            ['html' => $html],
            $options,
        ));
    }

    /**
     * Generate PDF from URL
     */
    public function fromUrl(string $url, array $options = []): array
    {
        return $this->post('/api/pdf/generate-from-url', array_merge(
            ['url' => $url],
            $options,
        ));
    }

    /**
     * Generate PDF from Markdown
     *
     * @param string $markdown Markdown content
     * @param string $theme Theme name (business, modern, minimal, academic, creative)
     * @param array $options Additional options
     */
    public function fromMarkdown(string $markdown, string $theme = 'business', array $options = []): array
    {
        return $this->post('/api/pdf/generate-from-markdown', array_merge(
            ['markdown' => $markdown, 'theme' => $theme],
            $options,
        ));
    }

    /**
     * Get available Markdown themes
     */
    public function markdownThemes(): array
    {
        return $this->get('/api/markdown/themes');
    }

    /**
     * Preview Markdown as HTML
     *
     * @param string $markdown Markdown content
     * @param string $theme Theme name (default: business)
     */
    public function markdownPreview(string $markdown, string $theme = 'business'): array
    {
        return $this->post('/api/markdown/preview', [
            'markdown' => $markdown,
            'theme' => $theme,
        ]);
    }

    /**
     * Generate PDF from template
     */
    public function fromTemplate(string $slug, array $variables = [], array $options = []): array
    {
        return $this->post('/api/pdf/generate-from-template', array_merge(
            ['template' => $slug, 'variables' => $variables],
            $options,
        ));
    }

    /**
     * Generate PDF and send via email
     */
    public function fromHtmlWithEmail(string $html, string $to, string $subject = '', string $body = '', array $options = []): array
    {
        $email = ['to' => $to];
        if ($subject) $email['subject'] = $subject;
        if ($body) $email['body'] = $body;

        return $this->fromHtml($html, array_merge($options, ['email' => $email]));
    }

    /**
     * Get PDF file info
     */
    public function info(string $filename): array
    {
        return $this->get("/api/pdf/info/{$filename}");
    }

    /**
     * Download PDF file content
     */
    public function download(string $filename): string
    {
        $response = $this->client->get("/api/pdf/download/{$filename}");

        return $response->getBody()->getContents();
    }

    /**
     * Delete PDF file
     */
    public function delete(string $filename): array
    {
        return $this->request('DELETE', "/api/pdf/delete/{$filename}");
    }

    /**
     * Batch generate multiple PDFs
     */
    public function batch(array $items): array
    {
        return $this->post('/api/pdf/batch', ['items' => $items]);
    }

    /**
     * Get batch generation status
     */
    public function batchStatus(string $batchUuid): array
    {
        return $this->get("/api/pdf/batch/{$batchUuid}");
    }

    /**
     * Merge multiple PDFs into one
     */
    public function merge(array $filenames, array $options = []): array
    {
        return $this->post('/api/pdf/merge', array_merge(
            ['filenames' => $filenames],
            $options,
        ));
    }

    /**
     * Split a PDF by extracting specific pages
     *
     * @param string $filename Source PDF filename
     * @param string $pages Page specification (e.g. "1-3", "1,3,5", "1,3-5")
     * @param array $options Additional options (expiration_hours, max_downloads)
     */
    public function split(string $filename, string $pages, array $options = []): array
    {
        return $this->post('/api/pdf/split', array_merge(
            ['filename' => $filename, 'pages' => $pages],
            $options,
        ));
    }

    /**
     * Rotate PDF pages
     *
     * @param string $filename Source PDF filename
     * @param int $angle Rotation angle (90, 180, or 270)
     * @param string|null $pages Optional page specification (e.g. "1,3"). Rotates all if null.
     * @param array $options Additional options (expiration_hours, max_downloads)
     */
    public function rotate(string $filename, int $angle, ?string $pages = null, array $options = []): array
    {
        $data = array_merge(['filename' => $filename, 'angle' => $angle], $options);
        if ($pages !== null) {
            $data['pages'] = $pages;
        }

        return $this->post('/api/pdf/rotate', $data);
    }

    /**
     * Compress a PDF to reduce file size
     *
     * @param string $filename Source PDF filename
     * @param string $quality Compression quality (low, medium, high)
     * @param array $options Additional options (expiration_hours, max_downloads)
     */
    public function compress(string $filename, string $quality = 'medium', array $options = []): array
    {
        return $this->post('/api/pdf/compress', array_merge(
            ['filename' => $filename, 'quality' => $quality],
            $options,
        ));
    }

    /**
     * Extract text from a PDF
     *
     * @param string $filename Source PDF filename
     * @param array $options Additional options (pages, per_page)
     */
    public function extractText(string $filename, array $options = []): array
    {
        return $this->post('/api/pdf/extract-text', array_merge(
            ['filename' => $filename],
            $options,
        ));
    }

    /**
     * Read or set PDF metadata
     *
     * @param string $filename Source PDF filename
     * @param array $fields Metadata fields (title, author, subject, keywords, creator, producer)
     */
    public function metadata(string $filename, array $fields = []): array
    {
        return $this->post('/api/pdf/metadata', array_merge(
            ['filename' => $filename],
            $fields,
        ));
    }

    /**
     * Add page numbers to a PDF
     *
     * @param string $filename Source PDF filename
     * @param array $options Options (position, format, start_number, font_size)
     */
    public function pageNumbers(string $filename, array $options = []): array
    {
        return $this->post('/api/pdf/page-numbers', array_merge(
            ['filename' => $filename],
            $options,
        ));
    }

    /**
     * Convert PDF to PDF/A archival format
     *
     * @param string $filename Source PDF filename
     * @param string $conformance PDF/A conformance level (1b, 2b, 3b)
     * @param array $options Additional options
     */
    public function toPdfA(string $filename, string $conformance = '2b', array $options = []): array
    {
        return $this->post('/api/pdf/to-pdfa', array_merge(
            ['filename' => $filename, 'conformance' => $conformance],
            $options,
        ));
    }

    /**
     * Convert PDF pages to images
     *
     * @param string $filename Source PDF filename
     * @param string $format Output format (png or jpg)
     * @param array $options Additional options (pages, dpi)
     */
    public function toImage(string $filename, string $format = 'png', array $options = []): array
    {
        return $this->post('/api/pdf/to-image', array_merge(
            ['filename' => $filename, 'format' => $format],
            $options,
        ));
    }

    /**
     * Merge uploaded PDF files (and optionally server files) into one
     *
     * @param array<string|\SplFileInfo> $files File paths or SplFileInfo objects
     * @param array $serverFilenames Existing server filenames to include
     * @param array $options Additional options (expiration_hours, max_downloads, watermark)
     */
    public function mergeUpload(array $files, array $serverFilenames = [], array $options = []): array
    {
        $multipart = [];

        foreach ($files as $file) {
            $path = $file instanceof \SplFileInfo ? $file->getPathname() : (string) $file;
            $multipart[] = [
                'name' => 'files[]',
                'contents' => fopen($path, 'r'),
                'filename' => basename($path),
            ];
        }

        foreach ($serverFilenames as $filename) {
            $multipart[] = [
                'name' => 'filenames[]',
                'contents' => $filename,
            ];
        }

        foreach ($options as $key => $value) {
            $multipart[] = [
                'name' => $key,
                'contents' => (string) $value,
            ];
        }

        try {
            $response = $this->client->post('/api/pdf/merge-upload', [
                'multipart' => $multipart,
                'headers' => [
                    'Authorization' => "Bearer {$this->apiKey}",
                    'Accept' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $body = json_decode($e->getResponse()->getBody()->getContents(), true);

                throw new FunbrewException(
                    $body['message'] ?? 'API request failed',
                    $e->getResponse()->getStatusCode(),
                    $e,
                );
            }

            throw new FunbrewException('Network error: '.$e->getMessage(), 0, $e);
        }
    }

    // --- Templates (SaaS) ---

    /**
     * List all templates
     */
    public function templates(): array
    {
        return $this->get('/api/templates');
    }

    /**
     * Create a template
     *
     * @param string $name Template name
     * @param string $slug URL-safe slug (lowercase alphanumeric and hyphens)
     * @param string $htmlContent HTML content with {{ variable }} placeholders
     * @param array<int,array<string,mixed>>|null $variables Variable definitions ([{ "name": "...", "required": true }])
     */
    public function createTemplate(string $name, string $slug, string $htmlContent, ?array $variables = null): array
    {
        $payload = [
            'name' => $name,
            'slug' => $slug,
            'html_content' => $htmlContent,
        ];
        if ($variables !== null) {
            $payload['variables'] = $variables;
        }

        return $this->post('/api/templates', $payload);
    }

    /**
     * Update a template
     *
     * @param int $templateId Template ID
     * @param array $data Fields to update (name, html_content, variables, is_active)
     */
    public function updateTemplate(int $templateId, array $data): array
    {
        return $this->request('PUT', "/api/templates/{$templateId}", $data);
    }

    /**
     * Delete a template
     */
    public function deleteTemplate(int $templateId): array
    {
        return $this->request('DELETE', "/api/templates/{$templateId}");
    }

    // --- Webhooks (SaaS) ---

    /**
     * List all webhooks
     */
    public function webhooks(): array
    {
        return $this->get('/api/webhooks');
    }

    /**
     * Create a webhook
     *
     * @param string $url Webhook URL
     * @param array<int,string> $events Event names to subscribe to
     */
    public function createWebhook(string $url, array $events): array
    {
        return $this->post('/api/webhooks', [
            'url' => $url,
            'events' => $events,
        ]);
    }

    /**
     * Update a webhook
     *
     * @param int $webhookId Webhook ID
     * @param array $data Fields to update (url, events, is_active)
     */
    public function updateWebhook(int $webhookId, array $data): array
    {
        return $this->request('PUT', "/api/webhooks/{$webhookId}", $data);
    }

    /**
     * Delete a webhook
     */
    public function deleteWebhook(int $webhookId): array
    {
        return $this->request('DELETE', "/api/webhooks/{$webhookId}");
    }

    // --- Storage Config (SaaS) ---

    /**
     * Get current storage configuration
     */
    public function storageConfig(): array
    {
        return $this->get('/api/storage-config');
    }

    /**
     * Create storage configuration
     *
     * @param string $driver Storage driver ("s3" or "gcs")
     * @param array $config Driver config (must include "bucket")
     */
    public function createStorageConfig(string $driver, array $config): array
    {
        return $this->post('/api/storage-config', [
            'driver' => $driver,
            'config' => $config,
        ]);
    }

    /**
     * Update storage configuration
     *
     * @param array $data Fields to update (driver, config, is_active)
     */
    public function updateStorageConfig(array $data): array
    {
        return $this->request('PUT', '/api/storage-config', $data);
    }

    /**
     * Delete storage configuration
     */
    public function deleteStorageConfig(): array
    {
        return $this->request('DELETE', '/api/storage-config');
    }

    /**
     * Get usage information
     */
    public function usage(): array
    {
        return $this->get('/api/usage');
    }

    /**
     * Generate PDF in test mode (no count, TEST watermark)
     */
    public function test(string $html, array $options = []): array
    {
        return $this->fromHtml($html, array_merge($options, ['test' => true]));
    }

    private function get(string $path): array
    {
        return $this->request('GET', $path);
    }

    private function post(string $path, array $data): array
    {
        return $this->request('POST', $path, $data);
    }

    private function request(string $method, string $path, array $data = []): array
    {
        try {
            $options = $method === 'GET' ? [] : ['json' => $data];
            $response = $this->client->request($method, $path, $options);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $body = json_decode($e->getResponse()->getBody()->getContents(), true);

                throw new FunbrewException(
                    $body['message'] ?? 'API request failed',
                    $e->getResponse()->getStatusCode(),
                    $e,
                );
            }

            throw new FunbrewException('Network error: '.$e->getMessage(), 0, $e);
        }
    }
}
