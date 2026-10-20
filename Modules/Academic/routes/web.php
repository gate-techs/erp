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
use Modules\Academic\Http\Controllers\AcademicFeeStatisticsController;
use Modules\Academic\Http\Controllers\AcademicGuardianController;
use Modules\Academic\Http\Controllers\AcademicStudentController;
use Modules\Academic\Http\Controllers\AcademicStudentFeeController;
use Modules\Academic\Http\Controllers\AcademicStudentAttendanceController;
use Modules\Academic\Http\Controllers\AcademicTeacherController;
use Modules\Academic\Http\Controllers\TeacherGalleryController;
use Modules\Academic\Http\Controllers\DailyReportController;
use Modules\Academic\Http\Controllers\StudentWithdrawalRequestController;
use Modules\Academic\Http\Controllers\IncidentReportController;
use Modules\Academic\Http\Controllers\MedicationRequestController;
use Modules\Academic\Http\Controllers\CalenderController;
use Modules\Academic\Http\Controllers\AcademicDashboardController;
use Modules\Academic\Http\Controllers\ExecutiveManagementController;

Route::middleware(['auth', 'verified'])
    ->prefix('academic')
    ->name('academic.')
    ->group(function () {
        Route::get('dashboard', [AcademicDashboardController::class, 'index'])
            ->middleware('permission:academic.dashboard.view')
            ->name('dashboard');

        // Executive Management / الإدارة العليا - التقرير العام
        Route::get('executive-management/general-report', [ExecutiveManagementController::class, 'index'])
            ->name('executive-management.index');
        Route::get('executive-management/data', [ExecutiveManagementController::class, 'data'])
            ->name('executive-management.data');

        // Medication Requests / طلبات الأدوية
        Route::middleware('permission:academic.medication-requests.view')->group(function () {
            Route::get('medication-requests', [MedicationRequestController::class, 'index'])->name('medication-requests.index');
            Route::get('medication-requests/data', [MedicationRequestController::class, 'data'])->name('medication-requests.data');
            Route::get('medication-requests/{medication_request}', [MedicationRequestController::class, 'show'])->name('medication-requests.show');
        });
        Route::middleware('permission:academic.medication-requests.create')->group(function () {
            Route::post('medication-requests', [MedicationRequestController::class, 'store'])->name('medication-requests.store');
        });
        Route::middleware('permission:academic.medication-requests.edit')->group(function () {
            Route::match(['put', 'patch'], 'medication-requests/{medication_request}', [MedicationRequestController::class, 'update'])->name('medication-requests.update');
        });
        Route::delete('medication-requests/{medication_request}', [MedicationRequestController::class, 'destroy'])
            ->middleware('permission:academic.medication-requests.delete')
            ->name('medication-requests.destroy');
        Route::patch('medication-requests/{id}/done', [MedicationRequestController::class, 'done'])
            ->middleware('permission:academic.medication-requests.done')
            ->name('medication-requests.done');

        // Student Withdrawal Requests / طلبات انسحاب الطلاب
        Route::middleware('permission:academic.withdrawal-requests.view')->group(function () {
            Route::get('withdrawal-requests', [StudentWithdrawalRequestController::class, 'index'])->name('withdrawal-requests.index');
            Route::get('withdrawal-requests/data', [StudentWithdrawalRequestController::class, 'data'])->name('withdrawal-requests.data');
            Route::get('withdrawal-requests/branches/{institutionId}', [StudentWithdrawalRequestController::class, 'branches'])->name('withdrawal-requests.branches');
            Route::get('withdrawal-requests/students', [StudentWithdrawalRequestController::class, 'students'])->name('withdrawal-requests.students');
            Route::get('withdrawal-requests/{withdrawal_request}', [StudentWithdrawalRequestController::class, 'show'])->name('withdrawal-requests.show');
        });
        Route::middleware('permission:academic.withdrawal-requests.create')->group(function () {
            Route::get('withdrawal-requests/create', [StudentWithdrawalRequestController::class, 'create'])->name('withdrawal-requests.create');
            Route::post('withdrawal-requests', [StudentWithdrawalRequestController::class, 'store'])->name('withdrawal-requests.store');
        });
        Route::middleware('permission:academic.withdrawal-requests.edit')->group(function () {
            Route::get('withdrawal-requests/{withdrawal_request}/edit', [StudentWithdrawalRequestController::class, 'edit'])->name('withdrawal-requests.edit');
            Route::match(['put', 'patch'], 'withdrawal-requests/{withdrawal_request}', [StudentWithdrawalRequestController::class, 'update'])->name('withdrawal-requests.update');
        });
        Route::delete('withdrawal-requests/{id}', [StudentWithdrawalRequestController::class, 'destroy'])
            ->middleware('permission:academic.withdrawal-requests.delete')
            ->name('withdrawal-requests.destroy');

        // Daily Reports / التقارير اليومية
        Route::middleware('permission:academic.daily-reports.view')->group(function () {
            Route::get('daily-reports', [DailyReportController::class, 'index'])->name('daily-reports.index');
            Route::get('daily-reports/data', [DailyReportController::class, 'data'])->name('daily-reports.data');
            Route::get('daily-reports/branches/{institutionId}', [DailyReportController::class, 'branches'])->name('daily-reports.branches');
            Route::get('daily-reports/{daily_report}', [DailyReportController::class, 'show'])->name('daily-reports.show');
        });
        Route::middleware('permission:academic.daily-reports.create')->group(function () {
            Route::get('daily-reports/create', [DailyReportController::class, 'create'])->name('daily-reports.create');
            Route::post('daily-reports', [DailyReportController::class, 'store'])->name('daily-reports.store');
        });
        Route::middleware('permission:academic.daily-reports.edit')->group(function () {
            Route::get('daily-reports/{daily_report}/edit', [DailyReportController::class, 'edit'])->name('daily-reports.edit');
            Route::match(['put', 'patch'], 'daily-reports/{daily_report}', [DailyReportController::class, 'update'])->name('daily-reports.update');
        });
        Route::delete('daily-reports/{id}', [DailyReportController::class, 'destroy'])
            ->middleware('permission:academic.daily-reports.delete')
            ->name('daily-reports.destroy');

        // =============================================================
        // 1. Academic Years / Periods (الفترات والسنوات الدراسية)
        // =============================================================
        Route::middleware('permission:academic.years.view')->group(function () {
            Route::get('academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
            Route::get('academic-years/data', [AcademicYearController::class, 'data'])->name('academic-years.data');
            Route::get('academic-years/{id}', [AcademicYearController::class, 'show'])->name('academic-years.show')->whereNumber('id');
        });
        Route::post('academic-years', [AcademicYearController::class, 'store'])
            ->middleware('permission:academic.years.create')
            ->name('academic-years.store');
        Route::put('academic-years/{id}', [AcademicYearController::class, 'update'])
            ->middleware('permission:academic.years.edit')
            ->name('academic-years.update')
            ->whereNumber('id');
        Route::delete('academic-years/{id}', [AcademicYearController::class, 'destroy'])
            ->middleware('permission:academic.years.delete')
            ->name('academic-years.destroy')
            ->whereNumber('id');
        Route::patch('academic-years/{id}/toggle-status', [AcademicYearController::class, 'toggleStatus'])
            ->middleware('permission:academic.years.toggle-status')
            ->name('academic-years.toggle-status')
            ->whereNumber('id');
        Route::patch('academic-years/{id}/set-current', [AcademicYearController::class, 'setCurrent'])
            ->middleware('permission:academic.years.set-current')
            ->name('academic-years.set-current')
            ->whereNumber('id');

        // =============================================================
        // 2. Academic Stages (المراحل الدراسية)
        // =============================================================
        Route::middleware('permission:academic.stages.view')->group(function () {
            Route::get('stages', [AcademicStageController::class, 'index'])->name('stages.index');
            Route::get('stages/data', [AcademicStageController::class, 'data'])->name('stages.data');
            Route::get('stages/{id}', [AcademicStageController::class, 'show'])->name('stages.show')->whereNumber('id');
        });
        Route::post('stages', [AcademicStageController::class, 'store'])
            ->middleware('permission:academic.stages.create')
            ->name('stages.store');
        Route::put('stages/{id}', [AcademicStageController::class, 'update'])
            ->middleware('permission:academic.stages.edit')
            ->name('stages.update')
            ->whereNumber('id');
        Route::delete('stages/{id}', [AcademicStageController::class, 'destroy'])
            ->middleware('permission:academic.stages.delete')
            ->name('stages.destroy')
            ->whereNumber('id');
        Route::patch('stages/{id}/toggle-status', [AcademicStageController::class, 'toggleStatus'])
            ->middleware('permission:academic.stages.toggle-status')
            ->name('stages.toggle-status')
            ->whereNumber('id');

        // =============================================================
        // 3. Academic Classes (الفصول الدراسية)
        // =============================================================
        Route::middleware('permission:academic.classes.view')->group(function () {
            Route::get('classes', [AcademicClassController::class, 'index'])->name('classes.index');
            Route::get('classes/data', [AcademicClassController::class, 'data'])->name('classes.data');
            Route::get('classes/{id}', [AcademicClassController::class, 'show'])->name('classes.show')->whereNumber('id');
        });
        Route::post('classes', [AcademicClassController::class, 'store'])
            ->middleware('permission:academic.classes.create')
            ->name('classes.store');
        Route::put('classes/{id}', [AcademicClassController::class, 'update'])
            ->middleware('permission:academic.classes.edit')
            ->name('classes.update')
            ->whereNumber('id');
        Route::delete('classes/{id}', [AcademicClassController::class, 'destroy'])
            ->middleware('permission:academic.classes.delete')
            ->name('classes.destroy')
            ->whereNumber('id');
        Route::patch('classes/{id}/toggle-status', [AcademicClassController::class, 'toggleStatus'])
            ->middleware('permission:academic.classes.toggle-status')
            ->name('classes.toggle-status')
            ->whereNumber('id');

        // =============================================================
        // 4. Academic Subjects (المواد الدراسية)
        // =============================================================
        Route::middleware('permission:academic.subjects.view')->group(function () {
            Route::get('subjects', [AcademicSubjectController::class, 'index'])->name('subjects.index');
            Route::get('subjects/data', [AcademicSubjectController::class, 'data'])->name('subjects.data');
            Route::get('subjects/{id}', [AcademicSubjectController::class, 'show'])->name('subjects.show')->whereNumber('id');
            Route::get('subjects/attachments/{id}/download', [AcademicSubjectController::class, 'downloadAttachment'])->name('subjects.attachments.download')->whereNumber('id');
        });
        Route::middleware('permission:academic.subjects.board')->group(function () {
            Route::get('subjects/board', [AcademicSubjectController::class, 'board'])->name('subjects.board');
            Route::post('subjects/sync-stage', [AcademicSubjectController::class, 'syncSubjectStage'])->name('subjects.sync-stage');
        });
        Route::post('subjects', [AcademicSubjectController::class, 'store'])
            ->middleware('permission:academic.subjects.create')
            ->name('subjects.store');
        Route::post('subjects/{id}/clone', [AcademicSubjectController::class, 'clone'])
            ->middleware('permission:academic.subjects.clone')
            ->name('subjects.clone')
            ->whereNumber('id');
        Route::put('subjects/{id}', [AcademicSubjectController::class, 'update'])
            ->middleware('permission:academic.subjects.edit')
            ->name('subjects.update')
            ->whereNumber('id');
        Route::delete('subjects/{id}', [AcademicSubjectController::class, 'destroy'])
            ->middleware('permission:academic.subjects.delete')
            ->name('subjects.destroy')
            ->whereNumber('id');
        Route::patch('subjects/{id}/toggle-status', [AcademicSubjectController::class, 'toggleStatus'])
            ->middleware('permission:academic.subjects.toggle-status')
            ->name('subjects.toggle-status')
            ->whereNumber('id');
        Route::delete('subjects/attachments/{id}', [AcademicSubjectController::class, 'deleteAttachment'])
            ->middleware('permission:academic.subjects.edit')
            ->name('subjects.attachments.destroy')
            ->whereNumber('id');

        // =============================================================
        // 5. Lessons (الدروس التعليمية)
        // =============================================================
        Route::middleware('permission:academic.lessons.view')->group(function () {
            Route::get('lessons', [AcademicLessonController::class, 'index'])->name('lessons.index');
            Route::get('lessons/data', [AcademicLessonController::class, 'data'])->name('lessons.data');
            Route::get('lessons/{id}', [AcademicLessonController::class, 'show'])->name('lessons.show')->whereNumber('id');
            Route::get('lessons/attachments/{id}/download', [AcademicLessonController::class, 'downloadAttachment'])->name('lessons.attachments.download')->whereNumber('id');
            Route::get('lessons-ajax/stages/{institutionId}', [AcademicLessonController::class, 'getStages'])->name('lessons.ajax.stages');
            Route::get('lessons-ajax/subjects', [AcademicLessonController::class, 'getSubjects'])->name('lessons.ajax.subjects');
        });
        Route::post('lessons', [AcademicLessonController::class, 'store'])
            ->middleware('permission:academic.lessons.create')
            ->name('lessons.store');
        Route::put('lessons/{id}', [AcademicLessonController::class, 'update'])
            ->middleware('permission:academic.lessons.edit')
            ->name('lessons.update')
            ->whereNumber('id');
        Route::delete('lessons/{id}', [AcademicLessonController::class, 'destroy'])
            ->middleware('permission:academic.lessons.delete')
            ->name('lessons.destroy')
            ->whereNumber('id');
        Route::patch('lessons/{id}/toggle-status', [AcademicLessonController::class, 'toggleStatus'])
            ->middleware('permission:academic.lessons.toggle-status')
            ->name('lessons.toggle-status')
            ->whereNumber('id');
        Route::delete('lessons/attachments/{id}', [AcademicLessonController::class, 'deleteAttachment'])
            ->middleware('permission:academic.lessons.edit')
            ->name('lessons.attachments.destroy')
            ->whereNumber('id');

        // =============================================================
        // 6. Weekly Plans (الخطط الأسبوعية)
        // =============================================================
        Route::middleware('permission:academic.weekly-plans.view')->group(function () {
            Route::get('weekly-plans', [AcademicWeeklyPlanController::class, 'index'])->name('weekly-plans.index');
            Route::get('weekly-plans/data', [AcademicWeeklyPlanController::class, 'data'])->name('weekly-plans.data');
            Route::get('weekly-plans/{id}', [AcademicWeeklyPlanController::class, 'show'])->name('weekly-plans.show')->whereNumber('id');
            Route::get('weekly-plans/attachments/{id}/download', [AcademicWeeklyPlanController::class, 'downloadAttachment'])->name('weekly-plans.attachments.download')->whereNumber('id');
            Route::get('weekly-plans-ajax/branches/{institutionId}', [AcademicWeeklyPlanController::class, 'getBranches'])->name('weekly-plans.ajax.branches');
            Route::get('weekly-plans-ajax/stages/{institutionId}', [AcademicWeeklyPlanController::class, 'getStages'])->name('weekly-plans.ajax.stages');
            Route::get('weekly-plans-ajax/classes', [AcademicWeeklyPlanController::class, 'getClasses'])->name('weekly-plans.ajax.classes');
        });
        Route::post('weekly-plans', [AcademicWeeklyPlanController::class, 'store'])
            ->middleware('permission:academic.weekly-plans.create')
            ->name('weekly-plans.store');
        Route::put('weekly-plans/{id}', [AcademicWeeklyPlanController::class, 'update'])
            ->middleware('permission:academic.weekly-plans.edit')
            ->name('weekly-plans.update')
            ->whereNumber('id');
        Route::delete('weekly-plans/{id}', [AcademicWeeklyPlanController::class, 'destroy'])
            ->middleware('permission:academic.weekly-plans.delete')
            ->name('weekly-plans.destroy')
            ->whereNumber('id');
        Route::patch('weekly-plans/{id}/toggle-status', [AcademicWeeklyPlanController::class, 'toggleStatus'])
            ->middleware('permission:academic.weekly-plans.toggle-status')
            ->name('weekly-plans.toggle-status')
            ->whereNumber('id');
        Route::delete('weekly-plans/attachments/{id}', [AcademicWeeklyPlanController::class, 'deleteAttachment'])
            ->middleware('permission:academic.weekly-plans.edit')
            ->name('weekly-plans.attachments.destroy')
            ->whereNumber('id');

        // =============================================================
        // 7. Monthly Plans (الخطط الشهرية)
        // =============================================================
        Route::middleware('permission:academic.monthly-plans.view')->group(function () {
            Route::get('monthly-plans', [AcademicMonthlyPlanController::class, 'index'])->name('monthly-plans.index');
            Route::get('monthly-plans/data', [AcademicMonthlyPlanController::class, 'data'])->name('monthly-plans.data');
            Route::get('monthly-plans/{id}', [AcademicMonthlyPlanController::class, 'show'])->name('monthly-plans.show')->whereNumber('id');
            Route::get('monthly-plans/attachments/{id}/download', [AcademicMonthlyPlanController::class, 'downloadAttachment'])->name('monthly-plans.attachments.download')->whereNumber('id');
            Route::get('monthly-plans-ajax/branches/{institutionId}', [AcademicMonthlyPlanController::class, 'getBranches'])->name('monthly-plans.ajax.branches');
            Route::get('monthly-plans-ajax/periods/{institutionId}', [AcademicMonthlyPlanController::class, 'getPeriods'])->name('monthly-plans.ajax.periods');
            Route::get('monthly-plans-ajax/stages/{institutionId}', [AcademicMonthlyPlanController::class, 'getStages'])->name('monthly-plans.ajax.stages');
            Route::get('monthly-plans-ajax/classes', [AcademicMonthlyPlanController::class, 'getClasses'])->name('monthly-plans.ajax.classes');
        });
        Route::post('monthly-plans', [AcademicMonthlyPlanController::class, 'store'])
            ->middleware('permission:academic.monthly-plans.create')
            ->name('monthly-plans.store');
        Route::put('monthly-plans/{id}', [AcademicMonthlyPlanController::class, 'update'])
            ->middleware('permission:academic.monthly-plans.edit')
            ->name('monthly-plans.update')
            ->whereNumber('id');
        Route::delete('monthly-plans/{id}', [AcademicMonthlyPlanController::class, 'destroy'])
            ->middleware('permission:academic.monthly-plans.delete')
            ->name('monthly-plans.destroy')
            ->whereNumber('id');
        Route::patch('monthly-plans/{id}/toggle-status', [AcademicMonthlyPlanController::class, 'toggleStatus'])
            ->middleware('permission:academic.monthly-plans.toggle-status')
            ->name('monthly-plans.toggle-status')
            ->whereNumber('id');
        Route::delete('monthly-plans/attachments/{id}', [AcademicMonthlyPlanController::class, 'deleteAttachment'])
            ->middleware('permission:academic.monthly-plans.edit')
            ->name('monthly-plans.attachments.destroy')
            ->whereNumber('id');

        // =============================================================
        // Fees & Packages (الرسوم والباقات)
        // =============================================================

        // 1. Fee Statistics / Dashboard (لوحة التحكم)
        Route::middleware('permission:academic.fee-statistics.view')->group(function () {
            Route::get('fee-statistics', [AcademicFeeStatisticsController::class, 'index'])->name('fee-statistics.index');
            Route::get('fee-statistics/stats', [AcademicFeeStatisticsController::class, 'stats'])->name('fee-statistics.stats');
        });

        // 2. Academic Fee Types / أنواع الرسوم
        Route::middleware('permission:academic.fee-types.view')->group(function () {
            Route::get('fee-types', [AcademicFeeTypeController::class, 'index'])->name('fee-types.index');
            Route::get('fee-types/data', [AcademicFeeTypeController::class, 'data'])->name('fee-types.data');
            Route::get('fee-types/{id}', [AcademicFeeTypeController::class, 'show'])->name('fee-types.show');
        });
        Route::post('fee-types', [AcademicFeeTypeController::class, 'store'])
            ->middleware('permission:academic.fee-types.create')
            ->name('fee-types.store');
        Route::put('fee-types/{id}', [AcademicFeeTypeController::class, 'update'])
            ->middleware('permission:academic.fee-types.edit')
            ->name('fee-types.update');
        Route::delete('fee-types/{id}', [AcademicFeeTypeController::class, 'destroy'])
            ->middleware('permission:academic.fee-types.delete')
            ->name('fee-types.destroy');
        Route::patch('fee-types/{id}/toggle-status', [AcademicFeeTypeController::class, 'toggleStatus'])
            ->middleware('permission:academic.fee-types.toggle-status')
            ->name('fee-types.toggle-status');

        // 3. Academic Fees / الرسوم الدراسية
        Route::middleware('permission:academic.fees.view')->group(function () {
            Route::get('fees', [AcademicFeeController::class, 'index'])->name('fees.index');
            Route::get('fees/data', [AcademicFeeController::class, 'data'])->name('fees.data');
            Route::get('fees/{id}', [AcademicFeeController::class, 'show'])->name('fees.show');
            Route::get('fees-ajax/branches/{institutionId}', [AcademicFeeController::class, 'getBranches'])->name('fees.ajax.branches');
            Route::get('fees-ajax/years/{institutionId}', [AcademicFeeController::class, 'getYears'])->name('fees.ajax.years');
            Route::get('fees-ajax/stages/{institutionId}', [AcademicFeeController::class, 'getStages'])->name('fees.ajax.stages');
        });
        Route::post('fees', [AcademicFeeController::class, 'store'])
            ->middleware('permission:academic.fees.create')
            ->name('fees.store');
        Route::put('fees/{id}', [AcademicFeeController::class, 'update'])
            ->middleware('permission:academic.fees.edit')
            ->name('fees.update');
        Route::delete('fees/{id}', [AcademicFeeController::class, 'destroy'])
            ->middleware('permission:academic.fees.delete')
            ->name('fees.destroy');
        Route::patch('fees/{id}/toggle-status', [AcademicFeeController::class, 'toggleStatus'])
            ->middleware('permission:academic.fees.toggle-status')
            ->name('fees.toggle-status');

        // 4. Student Fees, Installments & Payments / كشوفات رسوم الطلاب
        Route::middleware('permission:academic.student-fees.view')->group(function () {
            Route::get('student-fees', [AcademicStudentFeeController::class, 'index'])->name('student-fees.index');
            Route::get('student-fees/data', [AcademicStudentFeeController::class, 'data'])->name('student-fees.data');
            Route::get('student-fees/{id}', [AcademicStudentFeeController::class, 'show'])->name('student-fees.show');
            Route::get('student-fee-payments', [AcademicStudentFeeController::class, 'paymentsIndex'])->name('student-fees.payments.index');
            Route::get('student-fee-payments/data', [AcademicStudentFeeController::class, 'paymentsData'])->name('student-fees.payments.data');
        });
        Route::post('student-fees', [AcademicStudentFeeController::class, 'store'])
            ->middleware('permission:academic.student-fees.create')
            ->name('student-fees.store');
        Route::put('student-fees/{id}', [AcademicStudentFeeController::class, 'update'])
            ->middleware('permission:academic.student-fees.edit')
            ->name('student-fees.update');
        Route::delete('student-fees/{id}', [AcademicStudentFeeController::class, 'destroy'])
            ->middleware('permission:academic.student-fees.delete')
            ->name('student-fees.destroy');
        Route::post('student-fees/{id}/payments', [AcademicStudentFeeController::class, 'storePayment'])
            ->middleware('permission:academic.student-fees.payments.create')
            ->name('student-fees.payments.store');
        Route::put('student-fee-payments/{paymentId}', [AcademicStudentFeeController::class, 'updatePayment'])
            ->middleware('permission:academic.student-fees.edit')
            ->name('student-fees.payments.update');
        Route::delete('student-fee-payments/{paymentId}', [AcademicStudentFeeController::class, 'destroyPayment'])
            ->middleware('permission:academic.student-fees.delete')
            ->name('student-fees.payments.destroy');
        Route::patch('student-fee-payments/{paymentId}/toggle-status', [AcademicStudentFeeController::class, 'togglePaymentStatus'])
            ->middleware('permission:academic.student-fees.edit')
            ->name('student-fees.payments.toggle-status');

        // =============================================================
        // 1. Academic Guardians / أولياء الأمور
        // =============================================================
        Route::prefix('guardians')->name('guardians.')->group(function () {
            Route::get('/', [AcademicGuardianController::class, 'index'])->middleware('permission:academic.guardians.view')->name('index');
            Route::get('/data', [AcademicGuardianController::class, 'data'])->middleware('permission:academic.guardians.view')->name('data');
            Route::post('/', [AcademicGuardianController::class, 'store'])->middleware('permission:academic.guardians.create')->name('store');
            Route::get('/available-employees', [AcademicGuardianController::class, 'getAvailableEmployees'])->middleware('permission:academic.guardians.view')->name('available-employees');
            Route::get('/branches/{institutionId}', [AcademicGuardianController::class, 'getBranches'])->middleware('permission:academic.guardians.view')->name('branches');
            Route::get('/{id}', [AcademicGuardianController::class, 'show'])->middleware('permission:academic.guardians.view')->name('show');
            Route::get('/{id}/edit', [AcademicGuardianController::class, 'edit'])->middleware('permission:academic.guardians.edit')->name('edit');
            Route::put('/{id}', [AcademicGuardianController::class, 'update'])->middleware('permission:academic.guardians.edit')->name('update');
            Route::delete('/{id}', [AcademicGuardianController::class, 'destroy'])->middleware('permission:academic.guardians.delete')->name('destroy');
            Route::patch('/{id}/toggle-status', [AcademicGuardianController::class, 'toggleStatus'])->middleware('permission:academic.guardians.toggle-status')->name('toggle-status');
        });

        // =============================================================
        // 2. Academic Students / الطلاب
        // =============================================================
        Route::prefix('students')->name('students.')->group(function () {
            Route::get('/', [AcademicStudentController::class, 'index'])->middleware('permission:academic.students.view')->name('index');
            Route::get('/data', [AcademicStudentController::class, 'data'])->middleware('permission:academic.students.view')->name('data');
            Route::get('/create', [AcademicStudentController::class, 'create'])->middleware('permission:academic.students.create')->name('create');
            Route::post('/', [AcademicStudentController::class, 'store'])->middleware('permission:academic.students.create')->name('store');
            Route::get('/{id}', [AcademicStudentController::class, 'show'])->middleware('permission:academic.students.view')->name('show');
            Route::get('/{id}/edit', [AcademicStudentController::class, 'edit'])->middleware('permission:academic.students.edit')->name('edit');
            Route::put('/{id}', [AcademicStudentController::class, 'update'])->middleware('permission:academic.students.edit')->name('update');
            Route::delete('/{id}', [AcademicStudentController::class, 'destroy'])->middleware('permission:academic.students.delete')->name('destroy');
            Route::patch('/{id}/toggle-status', [AcademicStudentController::class, 'toggleStatus'])->middleware('permission:academic.students.toggle-status')->name('toggle-status');
        });

        // Student AJAX Dynamic Dropdowns
        Route::prefix('students-ajax')->name('students.ajax.')->group(function () {
            Route::get('branches/{institutionId}', [AcademicStudentController::class, 'getBranches'])->middleware('permission:academic.students.view')->name('branches');
            Route::get('years/{institutionId}', [AcademicStudentController::class, 'getYears'])->middleware('permission:academic.students.view')->name('years');
            Route::get('stages/{institutionId}', [AcademicStudentController::class, 'getStages'])->middleware('permission:academic.students.view')->name('stages');
            Route::get('classes/{stageId}', [AcademicStudentController::class, 'getClasses'])->middleware('permission:academic.students.view')->name('classes');
            Route::get('guardians/{institutionId}', [AcademicStudentController::class, 'getGuardians'])->middleware('permission:academic.students.view')->name('guardians');
            Route::get('guardian-family/{guardianId}', [AcademicStudentController::class, 'getGuardianFamily'])->middleware('permission:academic.students.view')->name('guardian-family');
        });

        // =============================================================
        // 3. Student Attendance / تسجيل وحالات الحضور والغياب
        // =============================================================
        Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [AcademicStudentAttendanceController::class, 'index'])->middleware('permission:academic.attendance.view')->name('index');
            Route::get('/data', [AcademicStudentAttendanceController::class, 'data'])->middleware('permission:academic.attendance.view')->name('data');
            Route::get('/class/{classId}/roster', [AcademicStudentAttendanceController::class, 'classRoster'])->middleware('permission:academic.attendance.view')->name('class-roster');
            Route::post('/multi', [AcademicStudentAttendanceController::class, 'storeMulti'])->middleware('permission:academic.attendance.create')->name('store-multi');
            Route::post('/single', [AcademicStudentAttendanceController::class, 'storeSingle'])->middleware('permission:academic.attendance.create')->name('store-single');
            Route::post('/approve', [AcademicStudentAttendanceController::class, 'approve'])->middleware('permission:academic.attendance.edit')->name('approve');
            Route::post('/reject', [AcademicStudentAttendanceController::class, 'reject'])->middleware('permission:academic.attendance.edit')->name('reject');
            Route::get('/student/{studentId}/report', [AcademicStudentAttendanceController::class, 'studentReport'])->middleware('permission:academic.attendance.view')->name('student-report');
            Route::get('/guardian/{guardianId}/report', [AcademicStudentAttendanceController::class, 'guardianReport'])->middleware('permission:academic.attendance.view')->name('guardian-report');
        });

        // =============================================================
        // 4. Academic Teachers / المدرسون وتسكين المراحل والفصول
        // =============================================================
        Route::prefix('teachers')->name('teachers.')->group(function () {
            Route::get('/', [AcademicTeacherController::class, 'index'])->middleware('permission:academic.teachers.view')->name('index');
            Route::get('/data', [AcademicTeacherController::class, 'data'])->middleware('permission:academic.teachers.view')->name('data');
            Route::post('/', [AcademicTeacherController::class, 'store'])->middleware('permission:academic.teachers.create')->name('store');
            Route::get('/{id}', [AcademicTeacherController::class, 'show'])->middleware('permission:academic.teachers.view')->name('show');
            Route::put('/{id}', [AcademicTeacherController::class, 'update'])->middleware('permission:academic.teachers.edit')->name('update');
            Route::delete('/{id}', [AcademicTeacherController::class, 'destroy'])->middleware('permission:academic.teachers.delete')->name('destroy');
            Route::patch('/{id}/toggle-status', [AcademicTeacherController::class, 'toggleStatus'])->middleware('permission:academic.teachers.toggle-status')->name('toggle-status');
            Route::get('/{id}/allocation', [AcademicTeacherController::class, 'getAllocation'])->middleware('permission:academic.teachers.edit')->name('allocation');
            Route::post('/{id}/allocation', [AcademicTeacherController::class, 'saveAllocation'])->middleware('permission:academic.teachers.edit')->name('save-allocation');
        });

        // =============================================================
        // 5. Student Galleries / معرض صور وفيديوهات الطلاب
        // =============================================================
        Route::prefix('galleries')->name('galleries.')->group(function () {
            Route::get('/', [TeacherGalleryController::class, 'index'])->middleware('permission:academic.galleries.view')->name('index');
            Route::get('/data', [TeacherGalleryController::class, 'data'])->middleware('permission:academic.galleries.view')->name('data');
            Route::post('/', [TeacherGalleryController::class, 'store'])->middleware('permission:academic.galleries.create')->name('store');
            Route::get('/{id}', [TeacherGalleryController::class, 'show'])->middleware('permission:academic.galleries.view')->name('show');
            Route::put('/{id}', [TeacherGalleryController::class, 'update'])->middleware('permission:academic.galleries.edit')->name('update');
            Route::delete('/{id}', [TeacherGalleryController::class, 'destroy'])->middleware('permission:academic.galleries.delete')->name('destroy');
            Route::delete('/images/{id}', [TeacherGalleryController::class, 'deleteImage'])->middleware('permission:academic.galleries.delete')->name('images.destroy');
            Route::get('/class-students/{classroomId}', [TeacherGalleryController::class, 'getClassStudents'])->middleware('permission:academic.galleries.view')->name('class-students');
        });

        // Incident Reports / تقارير الحوادث والإصابات
        Route::middleware('permission:academic.incident-reports.view')->group(function () {
            Route::get('incident-reports', [IncidentReportController::class, 'index'])->name('incident-reports.index');
            Route::get('incident-reports/data', [IncidentReportController::class, 'data'])->name('incident-reports.data');
            Route::get('incident-reports/{id}/print', [IncidentReportController::class, 'print'])->name('incident-reports.print');
            Route::get('incident-reports/{incident_report}', [IncidentReportController::class, 'show'])->name('incident-reports.show');
        });
        Route::middleware('permission:academic.incident-reports.create')->group(function () {
            Route::get('incident-reports/create', [IncidentReportController::class, 'create'])->name('incident-reports.create');
            Route::post('incident-reports', [IncidentReportController::class, 'store'])->name('incident-reports.store');
        });
        Route::middleware('permission:academic.incident-reports.edit')->group(function () {
            Route::get('incident-reports/{incident_report}/edit', [IncidentReportController::class, 'edit'])->name('incident-reports.edit');
            Route::match(['put', 'patch'], 'incident-reports/{incident_report}', [IncidentReportController::class, 'update'])->name('incident-reports.update');
        });
        Route::delete('incident-reports/{incident_report}', [IncidentReportController::class, 'destroy'])
            ->middleware('permission:academic.incident-reports.delete')
            ->name('incident-reports.destroy');

        // =============================================================
        // Schedules & Events (الجداول والفعاليات)
        // =============================================================

        // 1. App Calendar / رزنامة التطبيق
        Route::middleware('permission:academic.calender.view')->group(function () {
            Route::get('calender', [CalenderController::class, 'index'])->name('calender.index');
            Route::get('calender/calendar-data', [CalenderController::class, 'calendarEvents'])->name('calender.calendar-data');
            Route::get('calender/{calender}', [CalenderController::class, 'show'])->name('calender.show');
        });
        Route::post('calender', [CalenderController::class, 'store'])
            ->middleware('permission:academic.calender.create')
            ->name('calender.store');
        Route::match(['put', 'patch'], 'calender/{calender}', [CalenderController::class, 'update'])
            ->middleware('permission:academic.calender.edit')
            ->name('calender.update');
        Route::delete('calender/{calender}', [CalenderController::class, 'destroy'])
            ->middleware('permission:academic.calender.delete')
            ->name('calender.destroy');
        Route::patch('calender/{id}/toggle-status', [CalenderController::class, 'toggleStatus'])
            ->middleware('permission:academic.calender.toggle-status')
            ->name('calender.toggle-status');

        // 2. Events / الفعاليات
        Route::middleware('permission:academic.events.view')->group(function () {
            Route::get('events', [\Modules\Academic\Http\Controllers\EventController::class, 'index'])->name('events.index');
            Route::get('events/data', [\Modules\Academic\Http\Controllers\EventController::class, 'data'])->name('events.data');
            Route::get('events/branches/{institutionId}', [\Modules\Academic\Http\Controllers\EventController::class, 'branches'])->name('events.branches');
            Route::get('events/classes/{branchId}', [\Modules\Academic\Http\Controllers\EventController::class, 'classes'])->name('events.classes');
            Route::get('events/{event}', [\Modules\Academic\Http\Controllers\EventController::class, 'show'])->name('events.show');
        });
        Route::middleware('permission:academic.events.create')->group(function () {
            Route::get('events/create', [\Modules\Academic\Http\Controllers\EventController::class, 'create'])->name('events.create');
            Route::post('events', [\Modules\Academic\Http\Controllers\EventController::class, 'store'])->name('events.store');
        });
        Route::middleware('permission:academic.events.edit')->group(function () {
            Route::get('events/{event}/edit', [\Modules\Academic\Http\Controllers\EventController::class, 'edit'])->name('events.edit');
            Route::match(['put', 'patch'], 'events/{event}', [\Modules\Academic\Http\Controllers\EventController::class, 'update'])->name('events.update');
        });
        Route::delete('events/{event}', [\Modules\Academic\Http\Controllers\EventController::class, 'destroy'])
            ->middleware('permission:academic.events.delete')
            ->name('events.destroy');
        Route::patch('events/{id}/toggle-status', [\Modules\Academic\Http\Controllers\EventController::class, 'toggleStatus'])
            ->middleware('permission:academic.events.toggle-status')
            ->name('events.toggle-status');

        // 3. Personal Calendar / الرزنامة الشخصية
        Route::middleware('permission:academic.personal-calendar.view')->group(function () {
            Route::get('personal-calendar', [\Modules\Academic\Http\Controllers\PersonalCalendarController::class, 'index'])->name('personal-calendar.index');
            Route::get('personal-calendar/calendar-data', [\Modules\Academic\Http\Controllers\PersonalCalendarController::class, 'calendarEvents'])->name('personal-calendar.calendar-data');
            Route::get('personal-calendar/{personal_calendar}', [\Modules\Academic\Http\Controllers\PersonalCalendarController::class, 'show'])->name('personal-calendar.show');
        });
        Route::post('personal-calendar', [\Modules\Academic\Http\Controllers\PersonalCalendarController::class, 'store'])
            ->middleware('permission:academic.personal-calendar.create')
            ->name('personal-calendar.store');
        Route::match(['put', 'patch'], 'personal-calendar/{personal_calendar}', [\Modules\Academic\Http\Controllers\PersonalCalendarController::class, 'update'])
            ->middleware('permission:academic.personal-calendar.edit')
            ->name('personal-calendar.update');
        Route::delete('personal-calendar/{personal_calendar}', [\Modules\Academic\Http\Controllers\PersonalCalendarController::class, 'destroy'])
            ->middleware('permission:academic.personal-calendar.delete')
            ->name('personal-calendar.destroy');
        Route::patch('personal-calendar/{id}/status', [\Modules\Academic\Http\Controllers\PersonalCalendarController::class, 'updateStatus'])
            ->middleware('permission:academic.personal-calendar.status')
            ->name('personal-calendar.status');

        // 4. Meetings Calendar / رزنامة الاجتماعات
        Route::middleware('permission:academic.meeting-calendar.view')->group(function () {
            Route::get('meeting-calendar', [\Modules\Academic\Http\Controllers\MeetingCalendarController::class, 'index'])->name('meeting-calendar.index');
            Route::get('meeting-calendar/calendar-data', [\Modules\Academic\Http\Controllers\MeetingCalendarController::class, 'calendarEvents'])->name('meeting-calendar.calendar-data');
            Route::get('meeting-calendar/sidebar-data', [\Modules\Academic\Http\Controllers\MeetingCalendarController::class, 'sidebarData'])->name('meeting-calendar.sidebar-data');
            Route::get('meeting-calendar/{meeting_calendar}', [\Modules\Academic\Http\Controllers\MeetingCalendarController::class, 'show'])->name('meeting-calendar.show');
        });
        Route::post('meeting-calendar', [\Modules\Academic\Http\Controllers\MeetingCalendarController::class, 'store'])
            ->middleware('permission:academic.meeting-calendar.create')
            ->name('meeting-calendar.store');
        Route::match(['put', 'patch'], 'meeting-calendar/{meeting_calendar}', [\Modules\Academic\Http\Controllers\MeetingCalendarController::class, 'update'])
            ->middleware('permission:academic.meeting-calendar.edit')
            ->name('meeting-calendar.update');
        Route::delete('meeting-calendar/{meeting_calendar}', [\Modules\Academic\Http\Controllers\MeetingCalendarController::class, 'destroy'])
            ->middleware('permission:academic.meeting-calendar.delete')
            ->name('meeting-calendar.destroy');
        Route::patch('meeting-calendar/{id}/status', [\Modules\Academic\Http\Controllers\MeetingCalendarController::class, 'updateStatus'])
            ->middleware('permission:academic.meeting-calendar.status')
            ->name('meeting-calendar.status');

        // 5. Interviews Calendar / رزنامة المقابلات
        Route::middleware('permission:academic.interviews_calender.view')->group(function () {
            Route::get('interviews_calender', [\Modules\Academic\Http\Controllers\InterviewsCalenderController::class, 'index'])->name('interviews_calender.index');
            Route::get('interviews_calender/calendar-data', [\Modules\Academic\Http\Controllers\InterviewsCalenderController::class, 'calendarEvents'])->name('interviews_calender.calendar-data');
            Route::get('interviews_calender/sidebar-data', [\Modules\Academic\Http\Controllers\InterviewsCalenderController::class, 'sidebarData'])->name('interviews_calender.sidebar-data');
            Route::get('interviews_calender/check-guardian', [\Modules\Academic\Http\Controllers\InterviewsCalenderController::class, 'checkGuardian'])->name('interviews_calender.check-guardian');
            Route::get('interviews_calender/branches/{institutionId}', [\Modules\Academic\Http\Controllers\InterviewsCalenderController::class, 'branches'])->name('interviews_calender.branches');
            Route::get('interviews_calender/{interviews_calender}', [\Modules\Academic\Http\Controllers\InterviewsCalenderController::class, 'show'])->name('interviews_calender.show');
        });
        Route::post('interviews_calender', [\Modules\Academic\Http\Controllers\InterviewsCalenderController::class, 'store'])
            ->middleware('permission:academic.interviews_calender.create')
            ->name('interviews_calender.store');
        Route::match(['put', 'patch'], 'interviews_calender/{interviews_calender}', [\Modules\Academic\Http\Controllers\InterviewsCalenderController::class, 'update'])
            ->middleware('permission:academic.interviews_calender.edit')
            ->name('interviews_calender.update');
        Route::delete('interviews_calender/{interviews_calender}', [\Modules\Academic\Http\Controllers\InterviewsCalenderController::class, 'destroy'])
            ->middleware('permission:academic.interviews_calender.delete')
            ->name('interviews_calender.destroy');
        Route::patch('interviews_calender/{id}/status', [\Modules\Academic\Http\Controllers\InterviewsCalenderController::class, 'updateStatus'])
            ->middleware('permission:academic.interviews_calender.status')
            ->name('interviews_calender.status');
    });
