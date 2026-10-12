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
use Modules\Academic\Repositories\AcademicGuardianRepository;
use Modules\Academic\Repositories\Contracts\AcademicGuardianRepositoryInterface;
use Modules\Academic\Repositories\AcademicStudentRepository;
use Modules\Academic\Repositories\Contracts\AcademicStudentRepositoryInterface;
use Modules\Academic\Repositories\AcademicStudentFeeRepository;
use Modules\Academic\Repositories\Contracts\AcademicStudentFeeRepositoryInterface;
use Modules\Academic\Repositories\AcademicStudentAttendanceRepository;
use Modules\Academic\Repositories\Contracts\AcademicStudentAttendanceRepositoryInterface;
use Modules\Academic\Repositories\Contracts\DailyReportRepositoryInterface;
use Modules\Academic\Repositories\DailyReportRepository;
use Modules\Academic\Repositories\Contracts\StudentWithdrawalRequestRepositoryInterface;
use Modules\Academic\Repositories\StudentWithdrawalRequestRepository;
use Modules\Academic\Repositories\Contracts\MedicationRequestRepositoryInterface;
use Modules\Academic\Repositories\MedicationRequestRepository;
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

        $this->app->bind(
            AcademicGuardianRepositoryInterface::class,
            AcademicGuardianRepository::class
        );

        $this->app->bind(
            AcademicStudentRepositoryInterface::class,
            AcademicStudentRepository::class
        );

        $this->app->bind(
            AcademicStudentFeeRepositoryInterface::class,
            AcademicStudentFeeRepository::class
        );

        $this->app->bind(
            AcademicStudentAttendanceRepositoryInterface::class,
            AcademicStudentAttendanceRepository::class
        );

        $this->app->bind(DailyReportRepositoryInterface::class, DailyReportRepository::class);
        $this->app->bind(StudentWithdrawalRequestRepositoryInterface::class, StudentWithdrawalRequestRepository::class);
        $this->app->bind(MedicationRequestRepositoryInterface::class, MedicationRequestRepository::class);

        $this->app->bind(
            \Modules\Academic\Repositories\Contracts\AcademicTeacherRepositoryInterface::class,
            \Modules\Academic\Repositories\AcademicTeacherRepository::class
        );

        $this->app->bind(
            \Modules\Academic\Repositories\Contracts\TeacherGalleryRepositoryInterface::class,
            \Modules\Academic\Repositories\TeacherGalleryRepository::class
        );

        $this->app->bind(
            \Modules\Academic\Repositories\Contracts\IncidentReportRepositoryInterface::class,
            \Modules\Academic\Repositories\IncidentReportRepository::class
        );

        $this->app->bind(
            \Modules\Academic\Repositories\Contracts\CalenderRepositoryInterface::class,
            \Modules\Academic\Repositories\CalenderRepository::class
        );

        $this->app->bind(
            \Modules\Academic\Repositories\Contracts\PersonalCalendarRepositoryInterface::class,
            \Modules\Academic\Repositories\PersonalCalendarRepository::class
        );

        $this->app->bind(
            \Modules\Academic\Repositories\Contracts\MeetingCalendarRepositoryInterface::class,
            \Modules\Academic\Repositories\MeetingCalendarRepository::class
        );

        $this->app->bind(
            \Modules\Academic\Repositories\Contracts\InterviewsCalenderRepositoryInterface::class,
            \Modules\Academic\Repositories\InterviewsCalenderRepository::class
        );

        $this->app->bind(
            \Modules\Academic\Repositories\Contracts\EventRepositoryInterface::class,
            \Modules\Academic\Repositories\EventRepository::class
        );
    }
}
