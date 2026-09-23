<?php

namespace Modules\AI\Services\Providers;

use Illuminate\Support\Facades\Http;
use Modules\AI\Contracts\TextGenerationProviderInterface;
use Modules\AI\Exceptions\AIException;

class OpenAIProvider implements TextGenerationProviderInterface
{
    public function __construct(private readonly array $config)
    {
    }

    public function generate(string $prompt, array $options = []): string
    {
        if (empty($this->config['api_key'])) {
            throw new AIException('OpenAI API key is not configured.');
        }

        $response = Http::timeout($this->config['timeout'] ?? 30)
            ->withToken($this->config['api_key'])
            ->acceptJson()
            ->post("{$this->config['base_url']}/chat/completions", [
                'model' => $options['model'] ?? $this->config['model'],
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'temperature' => $options['temperature'] ?? 0.2,
            ]);

        $model = $options['model'] ?? $this->config['model'];

        if ($response->failed()) {
            \Modules\AI\Models\AIUsageLog::record([
                'provider_code' => $this->config['code'] ?? 'openai',
                'driver' => 'openai',
                'model' => $model,
                'task_code' => $options['task_code'] ?? ($this->config['task_code'] ?? null),
                'status' => 'failed',
                'error_message' => $response->json('error.message', $response->body()),
            ]);

            throw new AIException('OpenAI request failed: ' . $response->json('error.message', $response->body()));
        }

        $usage = $response->json('usage', []);
        $promptTokens = (int) ($usage['prompt_tokens'] ?? 0);
        $completionTokens = (int) ($usage['completion_tokens'] ?? 0);
        $totalTokens = (int) ($usage['total_tokens'] ?? ($promptTokens + $completionTokens));

        \Modules\AI\Models\AIUsageLog::record([
            'provider_code' => $this->config['code'] ?? 'openai',
            'driver' => 'openai',
            'model' => $model,
            'task_code' => $options['task_code'] ?? ($this->config['task_code'] ?? null),
            'prompt_tokens' => $promptTokens,
            'completion_tokens' => $completionTokens,
            'total_tokens' => $totalTokens,
            'status' => 'success',
        ]);

        $text = $response->json('choices.0.message.content');

        if (!is_string($text) || trim($text) === '') {
            throw new AIException('OpenAI returned an empty response.');
        }

        return trim($text);
    }
}
