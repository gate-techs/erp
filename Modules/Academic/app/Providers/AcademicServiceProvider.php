<?php

namespace Modules\Academic\Providers;

use Modules\Academic\Repositories\AcademicClassRepository;
use Modules\Academic\Repositories\Contracts\AcademicClassRepositoryInterface;
use Modules\Academic\Repositories\AcademicStageRepository;
use Modules\Academic\Repositories\Contracts\AcademicStageRepositoryInterface;
use Modules\Academic\Repositories\AcademicYearRepository;
use Modules\Academic\Repositories\Contracts\AcademicYearRepositoryInterface;
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
    }
}
