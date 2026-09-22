<?php

namespace Modules\AuditLog\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class AuditLogServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'AuditLog';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'auditlog';

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        RouteServiceProvider::class,
    ];

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        parent::boot();

        $this->loadJsonTranslationsFrom(__DIR__ . '/../../lang');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'auditlog');

        \Modules\AuditLog\Services\AuditConnectionService::applyDynamicConnection();
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        parent::register();

        $this->app->bind(
            \Modules\AuditLog\Repositories\Contracts\AuditLogRepositoryInterface::class,
            \Modules\AuditLog\Repositories\AuditLogRepository::class
        );
    }
}
