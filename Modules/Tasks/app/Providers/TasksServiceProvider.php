<?php

namespace Modules\Tasks\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;
use Modules\Tasks\Repositories\Contracts\TaskGroupRepositoryInterface;
use Modules\Tasks\Repositories\Contracts\TaskRepositoryInterface;
use Modules\Tasks\Repositories\TaskGroupRepository;
use Modules\Tasks\Repositories\TaskRepository;

class TasksServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Tasks';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'tasks';

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    //-----------------------------------------
    public function register(): void
    {
        parent::register();

        $this->app->bind(
            TaskGroupRepositoryInterface::class,
            TaskGroupRepository::class
        );

        $this->app->bind(
            TaskRepositoryInterface::class,
            TaskRepository::class
        );
    }

    //-----------------------------------------
    public function boot(): void
    {
        parent::boot();

        $this->loadJsonTranslationsFrom(__DIR__ . '/../../lang');
    }
}
