<?php

namespace Modules\AI\Services;

use Illuminate\Contracts\Container\Container;
use InvalidArgumentException;
use Modules\AI\Contracts\TextGenerationProviderInterface;
use Modules\AI\Contracts\TranslationProviderInterface;
use Modules\AI\Models\AIProvider;
use Modules\AI\Models\AITask;
use Modules\AI\Services\Providers\DeepLProvider;
use Modules\AI\Services\Providers\GeminiProvider;
use Modules\AI\Services\Providers\OpenAIProvider;

class AIService
{
    public function __construct(private readonly Container $container)
    {
    }

    public function translationProvider(?string $provider = null): TranslationProviderInterface
    {
        $name = $provider ?: $this->providerForTask('translation');
        $config = $this->providerConfig($name);
        $config['code'] = $name;
        $config['task_code'] = 'translation';

        return match ($config['driver']) {
            'gemini' => new GeminiProvider($config),
            'deepl' => new DeepLProvider($config),
            default => throw new InvalidArgumentException("Unsupported translation provider [{$name}]."),
        };
    }

    public function textGenerationProvider(?string $provider = null, string $task = 'text_generation'): TextGenerationProviderInterface
    {
        $name = $provider ?: $this->providerForTask($task);
        $config = $this->providerConfig($name);
        $config['code'] = $name;
        $config['task_code'] = $task;

        return match ($config['driver']) {
            'gemini' => new GeminiProvider($config),
            'openai' => new OpenAIProvider($config),
            default => throw new InvalidArgumentException("Unsupported text generation provider [{$name}]."),
        };
    }

    public function translate(
        string $text,
        string $sourceLanguage,
        string $targetLanguage,
        ?string $provider = null
    ): string {
        return $this->translationProvider($provider)->translate($text, $sourceLanguage, $targetLanguage);
    }

    public function generate(string $prompt, array $options = [], ?string $provider = null, string $task = 'text_generation'): string
    {
        return $this->textGenerationProvider($provider, $task)->generate($prompt, $options);
    }

    private function providerConfig(string $name): array
    {
        $databaseProvider = AIProvider::query()
            ->where('code', $name)
            ->where('is_active', true)
            ->first();

        $config = config("ai.providers.{$name}", []);

        if ($databaseProvider) {
            $config = array_merge(
                config("ai.providers.{$databaseProvider->driver}", []),
                $config,
            );

            $config = array_merge($config, array_filter([
                'driver' => $databaseProvider->driver,
                'api_key' => $databaseProvider->api_key,
                'model' => $databaseProvider->model,
                'base_url' => $databaseProvider->base_url,
            ], static fn ($value) => $value !== null && $value !== ''));
            $config['settings'] = array_merge($config['settings'] ?? [], $databaseProvider->settings ?? []);
        }

        if (!is_array($config)) {
            throw new InvalidArgumentException("AI provider [{$name}] is not configured.");
        }

        return $config;
    }

    private function providerForTask(string $taskCode): string
    {
        $task = AITask::query()
            ->where('code', $taskCode)
            ->where('is_active', true)
            ->with(['providers' => fn ($query) => $query
                ->where('ai_providers.is_active', true)
                ->wherePivot('is_active', true)
                ->orderByPivot('is_default', 'desc')
                ->orderByPivot('priority')])
            ->first();

        $provider = $task?->providers->first();

        return $provider?->code ?: config('ai.default_provider', 'gemini');
    }
}
