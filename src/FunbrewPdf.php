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
