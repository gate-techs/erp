<?php

use Illuminate\Support\Facades\Route;
use Modules\Academic\Http\Controllers\AcademicClassController;
use Modules\Academic\Http\Controllers\AcademicLessonController;
use Modules\Academic\Http\Controllers\AcademicStageController;
use Modules\Academic\Http\Controllers\AcademicSubjectController;
use Modules\Academic\Http\Controllers\AcademicMonthlyPlanController;
use Modules\Academic\Http\Controllers\AcademicWeeklyPlanController;
use Modules\Academic\Http\Controllers\AcademicYearController;
use Modules\Academic\Http\Controllers\AcademicFeeController;
use Modules\Academic\Http\Controllers\AcademicFeeTypeController;

Route::middleware(['auth', 'verified'])
    ->prefix('academic')
    ->name('academic.')
    ->group(function () {
        Route::get('academic-years', [AcademicYearController::class, 'index'])
            ->name('academic-years.index');
        Route::get('academic-years/data', [AcademicYearController::class, 'data'])
            ->name('academic-years.data');
        Route::post('academic-years', [AcademicYearController::class, 'store'])
            ->name('academic-years.store');
        Route::get('academic-years/{id}', [AcademicYearController::class, 'show'])
            ->name('academic-years.show');
        Route::put('academic-years/{id}', [AcademicYearController::class, 'update'])
            ->name('academic-years.update');
        Route::delete('academic-years/{id}', [AcademicYearController::class, 'destroy'])
            ->name('academic-years.destroy');
        Route::patch('academic-years/{id}/toggle-status', [AcademicYearController::class, 'toggleStatus'])
            ->name('academic-years.toggle-status');
        Route::patch('academic-years/{id}/set-current', [AcademicYearController::class, 'setCurrent'])
            ->name('academic-years.set-current');

        Route::get('stages', [AcademicStageController::class, 'index'])
            ->name('stages.index');
        Route::get('stages/data', [AcademicStageController::class, 'data'])
            ->name('stages.data');
        Route::post('stages', [AcademicStageController::class, 'store'])
            ->name('stages.store');
        Route::get('stages/{id}', [AcademicStageController::class, 'show'])
            ->name('stages.show');
        Route::put('stages/{id}', [AcademicStageController::class, 'update'])
            ->name('stages.update');
        Route::delete('stages/{id}', [AcademicStageController::class, 'destroy'])
            ->name('stages.destroy');
        Route::patch('stages/{id}/toggle-status', [AcademicStageController::class, 'toggleStatus'])
            ->name('stages.toggle-status');

        Route::get('classes', [AcademicClassController::class, 'index'])
            ->name('classes.index');
        Route::get('classes/data', [AcademicClassController::class, 'data'])
            ->name('classes.data');
        Route::post('classes', [AcademicClassController::class, 'store'])
            ->name('classes.store');
        Route::get('classes/{id}', [AcademicClassController::class, 'show'])
            ->name('classes.show');
        Route::put('classes/{id}', [AcademicClassController::class, 'update'])
            ->name('classes.update');
        Route::delete('classes/{id}', [AcademicClassController::class, 'destroy'])
            ->name('classes.destroy');
        Route::patch('classes/{id}/toggle-status', [AcademicClassController::class, 'toggleStatus'])
            ->name('classes.toggle-status');

        Route::get('subjects', [AcademicSubjectController::class, 'index'])
            ->name('subjects.index');
        Route::get('subjects/board', [AcademicSubjectController::class, 'board'])
            ->name('subjects.board');
        Route::post('subjects/sync-stage', [AcademicSubjectController::class, 'syncSubjectStage'])
            ->name('subjects.sync-stage');
        Route::get('subjects/data', [AcademicSubjectController::class, 'data'])
            ->name('subjects.data');
        Route::post('subjects', [AcademicSubjectController::class, 'store'])
            ->name('subjects.store');
        Route::get('subjects/{id}', [AcademicSubjectController::class, 'show'])
            ->name('subjects.show');
        Route::put('subjects/{id}', [AcademicSubjectController::class, 'update'])
            ->name('subjects.update');
        Route::delete('subjects/{id}', [AcademicSubjectController::class, 'destroy'])
            ->name('subjects.destroy');
        Route::patch('subjects/{id}/toggle-status', [AcademicSubjectController::class, 'toggleStatus'])
            ->name('subjects.toggle-status');
        Route::post('subjects/{id}/clone', [AcademicSubjectController::class, 'clone'])
            ->name('subjects.clone');
        Route::delete('subjects/attachments/{id}', [AcademicSubjectController::class, 'deleteAttachment'])
            ->name('subjects.attachments.destroy');
        Route::get('subjects/attachments/{id}/download', [AcademicSubjectController::class, 'downloadAttachment'])
            ->name('subjects.attachments.download');

        // Lessons
        Route::get('lessons', [AcademicLessonController::class, 'index'])
            ->name('lessons.index');
        Route::get('lessons/data', [AcademicLessonController::class, 'data'])
            ->name('lessons.data');
        Route::post('lessons', [AcademicLessonController::class, 'store'])
            ->name('lessons.store');
        Route::get('lessons/{id}', [AcademicLessonController::class, 'show'])
            ->name('lessons.show');
        Route::put('lessons/{id}', [AcademicLessonController::class, 'update'])
            ->name('lessons.update');
        Route::delete('lessons/{id}', [AcademicLessonController::class, 'destroy'])
            ->name('lessons.destroy');
        Route::patch('lessons/{id}/toggle-status', [AcademicLessonController::class, 'toggleStatus'])
            ->name('lessons.toggle-status');
        Route::delete('lessons/attachments/{id}', [AcademicLessonController::class, 'deleteAttachment'])
            ->name('lessons.attachments.destroy');
        Route::get('lessons/attachments/{id}/download', [AcademicLessonController::class, 'downloadAttachment'])
            ->name('lessons.attachments.download');
        Route::get('lessons-ajax/stages/{institutionId}', [AcademicLessonController::class, 'getStages'])
            ->name('lessons.ajax.stages');
        Route::get('lessons-ajax/subjects', [AcademicLessonController::class, 'getSubjects'])
            ->name('lessons.ajax.subjects');

        // Weekly Plans
        Route::get('weekly-plans', [AcademicWeeklyPlanController::class, 'index'])
            ->name('weekly-plans.index');
        Route::get('weekly-plans/data', [AcademicWeeklyPlanController::class, 'data'])
            ->name('weekly-plans.data');
        Route::post('weekly-plans', [AcademicWeeklyPlanController::class, 'store'])
            ->name('weekly-plans.store');
        Route::get('weekly-plans/{id}', [AcademicWeeklyPlanController::class, 'show'])
            ->name('weekly-plans.show');
        Route::put('weekly-plans/{id}', [AcademicWeeklyPlanController::class, 'update'])
            ->name('weekly-plans.update');
        Route::delete('weekly-plans/{id}', [AcademicWeeklyPlanController::class, 'destroy'])
            ->name('weekly-plans.destroy');
        Route::patch('weekly-plans/{id}/toggle-status', [AcademicWeeklyPlanController::class, 'toggleStatus'])
            ->name('weekly-plans.toggle-status');
        Route::delete('weekly-plans/attachments/{id}', [AcademicWeeklyPlanController::class, 'deleteAttachment'])
            ->name('weekly-plans.attachments.destroy');
        Route::get('weekly-plans/attachments/{id}/download', [AcademicWeeklyPlanController::class, 'downloadAttachment'])
            ->name('weekly-plans.attachments.download');
        Route::get('weekly-plans-ajax/branches/{institutionId}', [AcademicWeeklyPlanController::class, 'getBranches'])
            ->name('weekly-plans.ajax.branches');
        Route::get('weekly-plans-ajax/stages/{institutionId}', [AcademicWeeklyPlanController::class, 'getStages'])
            ->name('weekly-plans.ajax.stages');
        Route::get('weekly-plans-ajax/classes', [AcademicWeeklyPlanController::class, 'getClasses'])
            ->name('weekly-plans.ajax.classes');

        // Monthly Plans
        Route::get('monthly-plans', [AcademicMonthlyPlanController::class, 'index'])
            ->name('monthly-plans.index');
        Route::get('monthly-plans/data', [AcademicMonthlyPlanController::class, 'data'])
            ->name('monthly-plans.data');
        Route::post('monthly-plans', [AcademicMonthlyPlanController::class, 'store'])
            ->name('monthly-plans.store');
        Route::get('monthly-plans/{id}', [AcademicMonthlyPlanController::class, 'show'])
            ->name('monthly-plans.show');
        Route::put('monthly-plans/{id}', [AcademicMonthlyPlanController::class, 'update'])
            ->name('monthly-plans.update');
        Route::delete('monthly-plans/{id}', [AcademicMonthlyPlanController::class, 'destroy'])
            ->name('monthly-plans.destroy');
        Route::patch('monthly-plans/{id}/toggle-status', [AcademicMonthlyPlanController::class, 'toggleStatus'])
            ->name('monthly-plans.toggle-status');
        Route::delete('monthly-plans/attachments/{id}', [AcademicMonthlyPlanController::class, 'deleteAttachment'])
            ->name('monthly-plans.attachments.destroy');
        Route::get('monthly-plans/attachments/{id}/download', [AcademicMonthlyPlanController::class, 'downloadAttachment'])
            ->name('monthly-plans.attachments.download');
        Route::get('monthly-plans-ajax/branches/{institutionId}', [AcademicMonthlyPlanController::class, 'getBranches'])
            ->name('monthly-plans.ajax.branches');
        Route::get('monthly-plans-ajax/periods/{institutionId}', [AcademicMonthlyPlanController::class, 'getPeriods'])
            ->name('monthly-plans.ajax.periods');
        Route::get('monthly-plans-ajax/stages/{institutionId}', [AcademicMonthlyPlanController::class, 'getStages'])
            ->name('monthly-plans.ajax.stages');
        Route::get('monthly-plans-ajax/classes', [AcademicMonthlyPlanController::class, 'getClasses'])
            ->name('monthly-plans.ajax.classes');

        // Academic Fee Types
        Route::get('fee-types', [AcademicFeeTypeController::class, 'index'])
            ->name('fee-types.index');
        Route::get('fee-types/data', [AcademicFeeTypeController::class, 'data'])
            ->name('fee-types.data');
        Route::post('fee-types', [AcademicFeeTypeController::class, 'store'])
            ->name('fee-types.store');
        Route::get('fee-types/{id}', [AcademicFeeTypeController::class, 'show'])
            ->name('fee-types.show');
        Route::put('fee-types/{id}', [AcademicFeeTypeController::class, 'update'])
            ->name('fee-types.update');
        Route::delete('fee-types/{id}', [AcademicFeeTypeController::class, 'destroy'])
            ->name('fee-types.destroy');
        Route::patch('fee-types/{id}/toggle-status', [AcademicFeeTypeController::class, 'toggleStatus'])
            ->name('fee-types.toggle-status');

        // Academic Fees
        Route::get('fees', [AcademicFeeController::class, 'index'])
            ->name('fees.index');
        Route::get('fees/data', [AcademicFeeController::class, 'data'])
            ->name('fees.data');
        Route::post('fees', [AcademicFeeController::class, 'store'])
            ->name('fees.store');
        Route::get('fees/{id}', [AcademicFeeController::class, 'show'])
            ->name('fees.show');
        Route::put('fees/{id}', [AcademicFeeController::class, 'update'])
            ->name('fees.update');
        Route::delete('fees/{id}', [AcademicFeeController::class, 'destroy'])
            ->name('fees.destroy');
        Route::patch('fees/{id}/toggle-status', [AcademicFeeController::class, 'toggleStatus'])
            ->name('fees.toggle-status');
        Route::get('fees-ajax/branches/{institutionId}', [AcademicFeeController::class, 'getBranches'])
            ->name('fees.ajax.branches');
        Route::get('fees-ajax/years/{institutionId}', [AcademicFeeController::class, 'getYears'])
            ->name('fees.ajax.years');
        Route::get('fees-ajax/stages/{institutionId}', [AcademicFeeController::class, 'getStages'])
            ->name('fees.ajax.stages');
    });
