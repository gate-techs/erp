<?php

namespace Modules\AccessControl\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Modules\AccessControl\Repositories\Contracts\PermissionRepositoryInterface;
use Modules\AccessControl\Repositories\Contracts\RoleRepositoryInterface;
use Modules\AccessControl\Repositories\Contracts\UserRepositoryInterface;
use Modules\AccessControl\Repositories\PermissionRepository;
use Modules\AccessControl\Repositories\RoleRepository;
use Modules\AccessControl\Repositories\UserRepository;

class AccessControlServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'AccessControl';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'accesscontrol';

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
     * Define module schedules.
     * 
     * @param $schedule
     */
    // protected function configureSchedules(Schedule $schedule): void
    // {
    //     $schedule->command('inspire')->hourly();
    // }

    public function register(): void
    {
        parent::register();

        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    public function boot(): void
    {
        parent::boot();

        $this->loadJsonTranslationsFrom(__DIR__ . '/../../lang');
    }
}
