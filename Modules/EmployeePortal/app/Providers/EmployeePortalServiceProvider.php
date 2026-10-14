<?php

namespace Modules\EmployeePortal\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class EmployeePortalServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'EmployeePortal';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'employeeportal';

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
     * Register services and interface bindings.
     */
    public function register(): void
    {
        parent::register();

        // 1. Repositories Bindings
        $this->app->bind(
            \Modules\EmployeePortal\Repositories\Contracts\PortalEmployeeRepositoryInterface::class,
            \Modules\EmployeePortal\Repositories\PortalEmployeeRepository::class
        );

        $this->app->bind(
            \Modules\EmployeePortal\Repositories\Contracts\PortalAttendanceRepositoryInterface::class,
            \Modules\EmployeePortal\Repositories\PortalAttendanceRepository::class
        );

        $this->app->bind(
            \Modules\EmployeePortal\Repositories\Contracts\PortalPushSubscriptionRepositoryInterface::class,
            \Modules\EmployeePortal\Repositories\PortalPushSubscriptionRepository::class
        );

        // 2. Services Bindings
        $this->app->bind(
            \Modules\EmployeePortal\Services\Contracts\PortalAuthServiceInterface::class,
            \Modules\EmployeePortal\Services\PortalAuthService::class
        );

        $this->app->bind(
            \Modules\EmployeePortal\Services\Contracts\PortalAttendanceServiceInterface::class,
            \Modules\EmployeePortal\Services\PortalAttendanceService::class
        );

        $this->app->bind(
            \Modules\EmployeePortal\Services\Contracts\PortalPwaServiceInterface::class,
            \Modules\EmployeePortal\Services\PortalPwaService::class
        );

        $this->app->bind(
            \Modules\EmployeePortal\Services\Contracts\PortalProfileServiceInterface::class,
            \Modules\EmployeePortal\Services\PortalProfileService::class
        );

        $this->app->bind(
            \Modules\EmployeePortal\Services\Contracts\PortalLeaveServiceInterface::class,
            \Modules\EmployeePortal\Services\PortalLeaveService::class
        );

        $this->app->bind(
            \Modules\EmployeePortal\Services\Contracts\PortalPermissionServiceInterface::class,
            \Modules\EmployeePortal\Services\PortalPermissionService::class
        );

        $this->app->bind(
            \Modules\EmployeePortal\Services\Contracts\PortalDocumentServiceInterface::class,
            \Modules\EmployeePortal\Services\PortalDocumentService::class
        );

        $this->app->bind(
            \Modules\EmployeePortal\Services\Contracts\PortalPushSubscriptionServiceInterface::class,
            \Modules\EmployeePortal\Services\PortalPushSubscriptionService::class
        );
    }

    /**
     * Boot the application events and load module translations.
     */
    public function boot(): void
    {
        parent::boot();

        $this->loadJsonTranslationsFrom(__DIR__ . '/../../lang');
    }
}
