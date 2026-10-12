<?php

namespace Modules\Academic\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Models\AcademicLesson;
use Modules\Academic\Models\AcademicMonthlyPlan;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Models\AcademicSubject;
use Modules\Academic\Models\AcademicWeeklyPlan;
use Modules\Academic\Models\AcademicYear;

class AcademicDashboardController extends Controller
{
    public function index(): View
    {
        // 1. Current Academic Year
        $currentYear = AcademicYear::query()->current()->first()
            ?? AcademicYear::query()->active()->latest('id')->first();

        // 2. Core Counts for the 7 items only:
        // (Academic Years, Stages, Classes, Subjects, Lessons, Weekly Plans, Monthly Plans)
        $counts = [
            'years'                 => AcademicYear::query()->count(),
            'active_years'          => AcademicYear::query()->active()->count(),
            'stages'                => AcademicStage::query()->count(),
            'active_stages'         => AcademicStage::query()->where('status', AcademicStage::STATUS_ACTIVE)->count(),
            'classes'               => AcademicClass::query()->count(),
            'active_classes'        => AcademicClass::query()->where('status', AcademicClass::STATUS_ACTIVE)->count(),
            'subjects'              => AcademicSubject::query()->count(),
            'active_subjects'       => AcademicSubject::query()->where('status', AcademicSubject::STATUS_ACTIVE)->count(),
            'lessons'               => AcademicLesson::query()->count(),
            'active_lessons'        => AcademicLesson::query()->where('status', AcademicLesson::STATUS_ACTIVE)->count(),
            'weekly_plans'          => AcademicWeeklyPlan::query()->count(),
            'active_weekly_plans'   => AcademicWeeklyPlan::query()->where('status', AcademicWeeklyPlan::STATUS_ACTIVE)->count(),
            'monthly_plans'         => AcademicMonthlyPlan::query()->count(),
            'active_monthly_plans'  => AcademicMonthlyPlan::query()->where('status', AcademicMonthlyPlan::STATUS_ACTIVE)->count(),
        ];

        // 3. Stage Distribution (Classes & Subjects per Stage)
        $stageDistribution = AcademicStage::query()
            ->withCount([
                'classes' => fn ($query) => $query->where('status', AcademicClass::STATUS_ACTIVE),
                'subjects' => fn ($query) => $query->where('status', AcademicSubject::STATUS_ACTIVE),
            ])
            ->get()
            ->map(fn (AcademicStage $stage) => [
                'label'    => $stage->name ?: __('Unnamed stage'),
                'classes'  => (int) $stage->classes_count,
                'subjects' => (int) $stage->subjects_count,
            ])
            ->values();

        // 4. Content Distribution for ApexCharts (Odoo Colors)
        $contentDistribution = [
            ['label' => __('Subjects'), 'value' => $counts['subjects'], 'color' => '#714B67'],
            ['label' => __('Lessons'), 'value' => $counts['lessons'], 'color' => '#017E84'],
            ['label' => __('Weekly Plans'), 'value' => $counts['weekly_plans'], 'color' => '#28A745'],
            ['label' => __('Monthly Plans'), 'value' => $counts['monthly_plans'], 'color' => '#E59866'],
        ];

        // 5. Recent Academic Years
        $recentYears = AcademicYear::query()
            ->latest('start_date')
            ->latest('id')
            ->limit(5)
            ->get();

        // 6. Stages Detailed List
        $stagesList = AcademicStage::query()
            ->withCount(['classes', 'subjects'])
            ->latest('id')
            ->limit(6)
            ->get();

        // 7. Recent Classes
        $recentClasses = AcademicClass::query()
            ->with(['academicStage.translations'])
            ->latest('id')
            ->limit(5)
            ->get();

        // 8. Recent Lessons
        $recentLessons = AcademicLesson::query()
            ->with(['academicSubject.translations', 'academicStage.translations'])
            ->latest('id')
            ->limit(5)
            ->get();

        // 9. Recent Weekly & Monthly Plans
        $recentWeeklyPlans = AcademicWeeklyPlan::query()
            ->with(['academicStage.translations', 'academicClass.translations'])
            ->latest('id')
            ->limit(4)
            ->get();

        $recentMonthlyPlans = AcademicMonthlyPlan::query()
            ->with(['academicStage.translations'])
            ->latest('id')
            ->limit(4)
            ->get();

        // 10. Recent Subjects
        $recentSubjects = AcademicSubject::query()
            ->withCount('academicStages')
            ->latest('id')
            ->limit(5)
            ->get();

        return view('academic::dashboard.index', compact(
            'counts',
            'currentYear',
            'stageDistribution',
            'contentDistribution',
            'recentYears',
            'stagesList',
            'recentClasses',
            'recentLessons',
            'recentWeeklyPlans',
            'recentMonthlyPlans',
            'recentSubjects'
        ));
    }
}
