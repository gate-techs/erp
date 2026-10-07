<?php

namespace Modules\Academic\Providers;

use Modules\Academic\Repositories\AcademicClassRepository;
use Modules\Academic\Repositories\Contracts\AcademicClassRepositoryInterface;
use Modules\Academic\Repositories\AcademicStageRepository;
use Modules\Academic\Repositories\Contracts\AcademicStageRepositoryInterface;
use Modules\Academic\Repositories\AcademicYearRepository;
use Modules\Academic\Repositories\Contracts\AcademicYearRepositoryInterface;
use Modules\Academic\Repositories\AcademicSubjectRepository;
use Modules\Academic\Repositories\Contracts\AcademicSubjectRepositoryInterface;
use Modules\Academic\Repositories\AcademicLessonRepository;
use Modules\Academic\Repositories\Contracts\AcademicLessonRepositoryInterface;
use Modules\Academic\Repositories\AcademicWeeklyPlanRepository;
use Modules\Academic\Repositories\Contracts\AcademicWeeklyPlanRepositoryInterface;
use Modules\Academic\Repositories\AcademicMonthlyPlanRepository;
use Modules\Academic\Repositories\Contracts\AcademicMonthlyPlanRepositoryInterface;
use Modules\Academic\Repositories\AcademicFeeRepository;
use Modules\Academic\Repositories\Contracts\AcademicFeeRepositoryInterface;
use Modules\Academic\Repositories\AcademicFeeTypeRepository;
use Modules\Academic\Repositories\Contracts\AcademicFeeTypeRepositoryInterface;
use Nwidart\Modules\Support\ModuleServiceProvider;

class AcademicServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Academic';

    protected string $nameLower = 'academic';

    protected array $providers = [
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();

        $this->loadJsonTranslationsFrom(__DIR__ . '/../../lang');
    }

    public function register(): void
    {
        parent::register();

        $this->app->bind(
            AcademicYearRepositoryInterface::class,
            AcademicYearRepository::class
        );

        $this->app->bind(
            AcademicStageRepositoryInterface::class,
            AcademicStageRepository::class
        );

        $this->app->bind(
            AcademicClassRepositoryInterface::class,
            AcademicClassRepository::class
        );

        $this->app->bind(
            AcademicSubjectRepositoryInterface::class,
            AcademicSubjectRepository::class
        );

        $this->app->bind(
            AcademicLessonRepositoryInterface::class,
            AcademicLessonRepository::class
        );

        $this->app->bind(
            AcademicWeeklyPlanRepositoryInterface::class,
            AcademicWeeklyPlanRepository::class
        );

        $this->app->bind(
            AcademicMonthlyPlanRepositoryInterface::class,
            AcademicMonthlyPlanRepository::class
        );

        $this->app->bind(
            AcademicFeeRepositoryInterface::class,
            AcademicFeeRepository::class
        );

        $this->app->bind(
            AcademicFeeTypeRepositoryInterface::class,
            AcademicFeeTypeRepository::class
        );
    }
}
