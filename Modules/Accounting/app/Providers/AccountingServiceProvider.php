<?php

namespace Modules\Accounting\Providers;

use Modules\Accounting\Repositories\AccountRepository;
use Modules\Accounting\Repositories\Contracts\AccountRepositoryInterface;
use Modules\Accounting\Repositories\Contracts\EntityRepositoryInterface;
use Modules\Accounting\Repositories\Contracts\FiscalYearRepositoryInterface;
use Modules\Accounting\Repositories\Contracts\LockDateRepositoryInterface;
use Modules\Accounting\Repositories\EntityRepository;
use Modules\Accounting\Repositories\FiscalYearRepository;
use Modules\Accounting\Repositories\LockDateRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class AccountingServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Accounting';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'accounting';

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

    //--------------------------------------------------
    public function register(): void
    {
        parent::register();

        $this->app->bind(AccountRepositoryInterface::class, AccountRepository::class);
        $this->app->bind(EntityRepositoryInterface::class, EntityRepository::class);
        $this->app->bind(FiscalYearRepositoryInterface::class, FiscalYearRepository::class);
        $this->app->bind(LockDateRepositoryInterface::class, LockDateRepository::class);
        $this->app->bind(
            \Modules\Accounting\Repositories\Contracts\CostCenterRepositoryInterface::class,
            \Modules\Accounting\Repositories\CostCenterRepository::class
        );
    }

    //--------------------------------------------------
    public function boot(): void
    {
        parent::boot();

        $this->loadJsonTranslationsFrom(__DIR__ . '/../../lang');
    }
}
