<?php

namespace Modules\Settings\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class SettingsServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Settings';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'settings';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        parent::boot();

        $this->loadJsonTranslationsFrom(__DIR__ . '/../../lang');

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            try {
                $view->with('globalSetting', \Modules\Settings\Models\Setting::instance());
            } catch (\Throwable $e) {
                $view->with('globalSetting', null);
            }
        });
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        parent::register();

        $this->app->bind(
            \Modules\Settings\Repositories\Contracts\SettingRepositoryInterface::class,
            \Modules\Settings\Repositories\SettingRepository::class
        );

        $this->app->bind(
            \Modules\Settings\Repositories\Contracts\GeneralSettingRepositoryInterface::class,
            \Modules\Settings\Repositories\GeneralSettingRepository::class
        );

        $this->app->bind(
            \Modules\Settings\Repositories\Contracts\BankRepositoryInterface::class,
            \Modules\Settings\Repositories\BankRepository::class
        );

        $this->app->bind(
            \Modules\Settings\Repositories\Contracts\PrintTemplateRepositoryInterface::class,
            \Modules\Settings\Repositories\PrintTemplateRepository::class
        );

        $this->app->bind(
            \Modules\Settings\Repositories\Contracts\NotificationRepositoryInterface::class,
            \Modules\Settings\Repositories\NotificationRepository::class
        );

        $this->app->bind(
            \Modules\Settings\Repositories\Contracts\PushSubscriptionRepositoryInterface::class,
            \Modules\Settings\Repositories\PushSubscriptionRepository::class
        );

        $this->app->bind(
            \Modules\Settings\Repositories\Contracts\EmailTemplateRepositoryInterface::class,
            \Modules\Settings\Repositories\EmailTemplateRepository::class
        );

        $this->app->bind(
            \Modules\Settings\Services\Contracts\EmailTemplateServiceInterface::class,
            \Modules\Settings\Services\EmailTemplateService::class
        );

        $this->app->bind(
            \Modules\Settings\Services\Contracts\NotificationServiceInterface::class,
            \Modules\Settings\Services\NotificationService::class
        );

        $this->app->bind(
            \Modules\Settings\Services\Contracts\PushNotificationServiceInterface::class,
            \Modules\Settings\Services\PushNotificationService::class
        );
    }
}
