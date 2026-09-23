<?php

namespace Modules\AI\Services\Providers;

use Illuminate\Support\Facades\Http;
use Modules\AI\Contracts\TranslationProviderInterface;
use Modules\AI\Exceptions\AIException;

class DeepLProvider implements TranslationProviderInterface
{
    public function __construct(private readonly array $config)
    {
    }

    public function translate(string $text, string $sourceLanguage, string $targetLanguage): string
    {
        if (empty($this->config['api_key'])) {
            throw new AIException('DeepL API key is not configured.');
        }

        $response = Http::timeout($this->config['timeout'] ?? 30)
            ->withToken($this->config['api_key'])
            ->acceptJson()
            ->post("{$this->config['base_url']}/translate", [
                'text' => [$text],
                'source_lang' => strtoupper($sourceLanguage),
                'target_lang' => strtoupper($targetLanguage),
            ]);

        if ($response->failed()) {
            \Modules\AI\Models\AIUsageLog::record([
                'provider_code' => $this->config['code'] ?? 'deepl',
                'driver' => 'deepl',
                'model' => 'DeepL API',
                'task_code' => 'translation',
                'status' => 'failed',
                'error_message' => $response->json('message', $response->body()),
            ]);

            throw new AIException('DeepL request failed: ' . $response->json('message', $response->body()));
        }

        \Modules\AI\Models\AIUsageLog::record([
            'provider_code' => $this->config['code'] ?? 'deepl',
            'driver' => 'deepl',
            'model' => 'DeepL API',
            'task_code' => 'translation',
            'characters_count' => mb_strlen($text),
            'status' => 'success',
        ]);

        $translated = $response->json('translations.0.text');

        if (!is_string($translated) || trim($translated) === '') {
            throw new AIException('DeepL returned an empty response.');
        }

        return trim($translated);
    }
}
