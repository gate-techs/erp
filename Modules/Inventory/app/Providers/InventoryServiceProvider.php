<?php

namespace Modules\Inventory\Providers;

use Modules\Inventory\Repositories\Contracts\WarehouseRepositoryInterface;
use Modules\Inventory\Repositories\WarehouseRepository;
use Modules\Inventory\Repositories\Contracts\ItemCategoryRepositoryInterface;
use Modules\Inventory\Repositories\ItemCategoryRepository;
use Modules\Inventory\Repositories\Contracts\UnitRepositoryInterface;
use Modules\Inventory\Repositories\UnitRepository;
use Modules\Inventory\Repositories\Contracts\ItemRepositoryInterface;
use Modules\Inventory\Repositories\ItemRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class InventoryServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Inventory';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'inventory';

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

    //------------------------------------------
    public function register(): void
    {
        parent::register();

        $this->app->bind(WarehouseRepositoryInterface::class, WarehouseRepository::class);
        $this->app->bind(ItemCategoryRepositoryInterface::class, ItemCategoryRepository::class);
        $this->app->bind(UnitRepositoryInterface::class, UnitRepository::class);
        $this->app->bind(ItemRepositoryInterface::class, ItemRepository::class);
    }

    //------------------------------------------
    public function boot(): void
    {
        parent::boot();

        $this->loadJsonTranslationsFrom(__DIR__ . '/../../lang');
    }
}
