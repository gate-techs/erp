<?php

namespace Modules\Nursery\Providers;

use Illuminate\Console\Scheduling\Schedule;
use Modules\Nursery\Repositories\Contracts\NurseryBranchRepositoryInterface;
use Modules\Nursery\Repositories\Contracts\NurseryRepositoryInterface;
use Modules\Nursery\Repositories\NurseryBranchRepository;
use Modules\Nursery\Repositories\NurseryRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;

class NurseryServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Nursery';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'nursery';

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    //======================
    /**
     * Register services and interface bindings.
     */
    public function register(): void
    {
        parent::register();

        $this->app->bind(NurseryRepositoryInterface::class, NurseryRepository::class);
        $this->app->bind(NurseryBranchRepositoryInterface::class, NurseryBranchRepository::class);
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
