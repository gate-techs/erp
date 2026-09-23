<?php

namespace Modules\AI\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Modules\AI\Contracts\TextGenerationProviderInterface;
use Modules\AI\Contracts\TranslationProviderInterface;
use Modules\AI\Services\AIService;
use Modules\AI\Services\Providers\DeepLProvider;
use Modules\AI\Services\Providers\GeminiProvider;
use Modules\AI\Services\Providers\OpenAIProvider;

class AIServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'AI';

    protected string $nameLower = 'ai';

    protected array $providers = [
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->mergeConfigFrom(__DIR__ . '/../../config/config.php', 'ai');

        $this->app->singleton(AIService::class, function ($app) {
            return new AIService($app);
        });

        $this->app->bind(TranslationProviderInterface::class, function ($app) {
            return $app->make(AIService::class)->translationProvider();
        });

        $this->app->bind(TextGenerationProviderInterface::class, function ($app) {
            return $app->make(AIService::class)->textGenerationProvider();
        });
    }

    public function boot(): void
    {
        parent::boot();

        $this->loadJsonTranslationsFrom(__DIR__ . '/../../lang');

        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('ai.php'),
        ], 'ai-config');
    }
}
