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

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\HrSettingRepositoryInterface::class,
            \Modules\HR\Repositories\HrSettingRepository::class
        );

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\ShiftRepositoryInterface::class,
            \Modules\HR\Repositories\ShiftRepository::class
        );

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\WorkScheduleRepositoryInterface::class,
            \Modules\HR\Repositories\WorkScheduleRepository::class
        );

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\OfficialHolidayRepositoryInterface::class,
            \Modules\HR\Repositories\OfficialHolidayRepository::class
        );

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\EmployeeRepositoryInterface::class,
            \Modules\HR\Repositories\EmployeeRepository::class
        );

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\AttendanceRepositoryInterface::class,
            \Modules\HR\Repositories\AttendanceRepository::class
        );

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\AttendanceRawLogRepositoryInterface::class,
            \Modules\HR\Repositories\AttendanceRawLogRepository::class
        );

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\WorkPolicyRepositoryInterface::class,
            \Modules\HR\Repositories\WorkPolicyRepository::class
        );

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\LeaveRequestRepositoryInterface::class,
            \Modules\HR\Repositories\LeaveRequestRepository::class
        );

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\PermissionRequestRepositoryInterface::class,
            \Modules\HR\Repositories\PermissionRequestRepository::class
        );

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\LoanRepositoryInterface::class,
            \Modules\HR\Repositories\LoanRepository::class
        );

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\RewardRepositoryInterface::class,
            \Modules\HR\Repositories\RewardRepository::class
        );

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\PenaltyRepositoryInterface::class,
            \Modules\HR\Repositories\PenaltyRepository::class
        );

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\PayrollRepositoryInterface::class,
            \Modules\HR\Repositories\PayrollRepository::class
        );

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\WorkforcePlanRepositoryInterface::class,
            \Modules\HR\Repositories\WorkforcePlanRepository::class
        );

        $this->app->bind(
            \Modules\HR\Repositories\Contracts\JobRequisitionRepositoryInterface::class,
            \Modules\HR\Repositories\JobRequisitionRepository::class
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
