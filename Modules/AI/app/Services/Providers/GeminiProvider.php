<?php

namespace Modules\AI\Services\Providers;

use Illuminate\Support\Facades\Http;
use Modules\AI\Contracts\TextGenerationProviderInterface;
use Modules\AI\Contracts\TranslationProviderInterface;
use Modules\AI\Exceptions\AIException;

class GeminiProvider implements TranslationProviderInterface, TextGenerationProviderInterface
{
    public function __construct(private readonly array $config)
    {
    }

    public function translate(string $text, string $sourceLanguage, string $targetLanguage): string
    {
        $prompt = "Translate the following text from {$sourceLanguage} to {$targetLanguage}. Return only the translation, with no explanation:\n\n{$text}";

        return $this->generate($prompt);
    }

    public function generate(string $prompt, array $options = []): string
    {
        $apiKey = $this->config['api_key'] ?? null;

        if (!$apiKey) {
            throw new AIException('Gemini API key is not configured.');
        }

        $model = $options['model'] ?? $this->config['model'];
        $response = Http::timeout($this->config['timeout'] ?? 30)
            ->acceptJson()
            ->post("{$this->config['base_url']}/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [[
                    'parts' => [['text' => $prompt]],
                ]],
                'generationConfig' => array_filter([
                    'temperature' => $options['temperature'] ?? null,
                    'maxOutputTokens' => $options['max_output_tokens'] ?? null,
                ], static fn ($value) => $value !== null),
            ]);

        if ($response->failed()) {
            \Modules\AI\Models\AIUsageLog::record([
                'provider_code' => $this->config['code'] ?? 'gemini',
                'driver' => 'gemini',
                'model' => $model,
                'task_code' => $options['task_code'] ?? ($this->config['task_code'] ?? null),
                'status' => 'failed',
                'error_message' => $response->json('error.message', $response->body()),
            ]);

            throw new AIException('Gemini request failed: ' . $response->json('error.message', $response->body()));
        }

        $usage = $response->json('usageMetadata', []);
        $promptTokens = (int) ($usage['promptTokenCount'] ?? 0);
        $completionTokens = (int) ($usage['candidatesTokenCount'] ?? 0);
        $totalTokens = (int) ($usage['totalTokenCount'] ?? ($promptTokens + $completionTokens));

        \Modules\AI\Models\AIUsageLog::record([
            'provider_code' => $this->config['code'] ?? 'gemini',
            'driver' => 'gemini',
            'model' => $model,
            'task_code' => $options['task_code'] ?? ($this->config['task_code'] ?? null),
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'total_tokens' => $totalTokens,
            'status' => 'success',
        ]);

        $text = $response->json('candidates.0.content.parts.0.text');

        if (!is_string($text) || trim($text) === '') {
            throw new AIException('Gemini returned an empty response.');
        }

        return trim($text);
    }
}
