<?php

namespace Modules\HR\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class HRServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'HR';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'hr';

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

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\DepartmentRepositoryInterface::class,
            \Modules\HR\Repositories\DepartmentRepository::class
        );

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\JobTitleRepositoryInterface::class,
            \Modules\HR\Repositories\JobTitleRepository::class
        );
    }

    //======================
    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        parent::boot();

        $this->loadJsonTranslationsFrom(__DIR__ . '/../../lang');
    }
}
