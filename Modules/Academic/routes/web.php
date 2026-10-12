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

Route::middleware(['auth', 'verified'])
    ->prefix('academic')
    ->name('academic.')
    ->group(function () {
        Route::get('dashboard', [AcademicDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('medication-requests/data',[MedicationRequestController::class,'data'])->name('medication-requests.data');
        Route::patch('medication-requests/{id}/done',[MedicationRequestController::class,'done'])->name('medication-requests.done');
        Route::resource('medication-requests',MedicationRequestController::class)->only(['index','store','show','update','destroy']);

        Route::get('withdrawal-requests/data',[StudentWithdrawalRequestController::class,'data'])->name('withdrawal-requests.data');
        Route::get('withdrawal-requests/branches/{institutionId}',[StudentWithdrawalRequestController::class,'branches'])->name('withdrawal-requests.branches');
        Route::get('withdrawal-requests/students',[StudentWithdrawalRequestController::class,'students'])->name('withdrawal-requests.students');
        Route::resource('withdrawal-requests',StudentWithdrawalRequestController::class)->except(['destroy']);
        Route::delete('withdrawal-requests/{id}',[StudentWithdrawalRequestController::class,'destroy'])->name('withdrawal-requests.destroy');

        Route::get('daily-reports/data', [DailyReportController::class, 'data'])->name('daily-reports.data');
        Route::get('daily-reports/branches/{institutionId}', [DailyReportController::class, 'branches'])->name('daily-reports.branches');
        Route::resource('daily-reports', DailyReportController::class)->except(['destroy']);
        Route::delete('daily-reports/{id}', [DailyReportController::class, 'destroy'])->name('daily-reports.destroy');

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
        Route::get('fee-statistics', [AcademicFeeStatisticsController::class, 'index'])
            ->name('fee-statistics.index');
        Route::get('fee-statistics/stats', [AcademicFeeStatisticsController::class, 'stats'])
            ->name('fee-statistics.stats');
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

        // Student Fees, Installments & Payments
        Route::get('student-fees', [AcademicStudentFeeController::class, 'index'])
            ->name('student-fees.index');
        Route::get('student-fees/data', [AcademicStudentFeeController::class, 'data'])
            ->name('student-fees.data');
        Route::post('student-fees', [AcademicStudentFeeController::class, 'store'])
            ->name('student-fees.store');
        Route::put('student-fees/{id}', [AcademicStudentFeeController::class, 'update'])
            ->name('student-fees.update');
        Route::delete('student-fees/{id}', [AcademicStudentFeeController::class, 'destroy'])
            ->name('student-fees.destroy');
        Route::get('student-fee-payments', [AcademicStudentFeeController::class, 'paymentsIndex'])
            ->name('student-fees.payments.index');
        Route::get('student-fee-payments/data', [AcademicStudentFeeController::class, 'paymentsData'])
            ->name('student-fees.payments.data');
        Route::get('student-fees/{id}', [AcademicStudentFeeController::class, 'show'])
            ->name('student-fees.show');
        Route::post('student-fees/{id}/payments', [AcademicStudentFeeController::class, 'storePayment'])
            ->name('student-fees.payments.store');
        Route::put('student-fee-payments/{paymentId}', [AcademicStudentFeeController::class, 'updatePayment'])
            ->name('student-fees.payments.update');
        Route::delete('student-fee-payments/{paymentId}', [AcademicStudentFeeController::class, 'destroyPayment'])
            ->name('student-fees.payments.destroy');
        Route::patch('student-fee-payments/{paymentId}/toggle-status', [AcademicStudentFeeController::class, 'togglePaymentStatus'])
            ->name('student-fees.payments.toggle-status');

        // Academic Guardians
        Route::get('guardians', [AcademicGuardianController::class, 'index'])
            ->name('guardians.index');
        Route::get('guardians/data', [AcademicGuardianController::class, 'data'])
            ->name('guardians.data');
        Route::post('guardians', [AcademicGuardianController::class, 'store'])
            ->name('guardians.store');
        Route::get('guardians/available-employees', [AcademicGuardianController::class, 'getAvailableEmployees'])
            ->name('guardians.available-employees');
        Route::get('guardians/branches/{institutionId}', [AcademicGuardianController::class, 'getBranches'])
            ->name('guardians.branches');
        Route::get('guardians/{id}', [AcademicGuardianController::class, 'show'])
            ->name('guardians.show');
        Route::get('guardians/{id}/edit', [AcademicGuardianController::class, 'edit'])
            ->name('guardians.edit');
        Route::put('guardians/{id}', [AcademicGuardianController::class, 'update'])
            ->name('guardians.update');
        Route::delete('guardians/{id}', [AcademicGuardianController::class, 'destroy'])
            ->name('guardians.destroy');
        Route::patch('guardians/{id}/toggle-status', [AcademicGuardianController::class, 'toggleStatus'])
            ->name('guardians.toggle-status');

        // Academic Students
        Route::get('students', [AcademicStudentController::class, 'index'])
            ->name('students.index');
        Route::get('students/data', [AcademicStudentController::class, 'data'])
            ->name('students.data');
        Route::get('students/create', [AcademicStudentController::class, 'create'])
            ->name('students.create');
        Route::post('students', [AcademicStudentController::class, 'store'])
            ->name('students.store');
        Route::get('students/{id}', [AcademicStudentController::class, 'show'])
            ->name('students.show');
        Route::get('students/{id}/edit', [AcademicStudentController::class, 'edit'])
            ->name('students.edit');
        Route::put('students/{id}', [AcademicStudentController::class, 'update'])
            ->name('students.update');
        Route::delete('students/{id}', [AcademicStudentController::class, 'destroy'])
            ->name('students.destroy');
        Route::patch('students/{id}/toggle-status', [AcademicStudentController::class, 'toggleStatus'])
            ->name('students.toggle-status');

        // Student AJAX Dynamic Dropdowns
        Route::get('students-ajax/branches/{institutionId}', [AcademicStudentController::class, 'getBranches'])
            ->name('students.ajax.branches');
        Route::get('students-ajax/years/{institutionId}', [AcademicStudentController::class, 'getYears'])
            ->name('students.ajax.years');
        Route::get('students-ajax/stages/{institutionId}', [AcademicStudentController::class, 'getStages'])
            ->name('students.ajax.stages');
        Route::get('students-ajax/classes/{stageId}', [AcademicStudentController::class, 'getClasses'])
            ->name('students.ajax.classes');
        Route::get('students-ajax/guardians/{institutionId}', [AcademicStudentController::class, 'getGuardians'])
            ->name('students.ajax.guardians');
        Route::get('students-ajax/guardian-family/{guardianId}', [AcademicStudentController::class, 'getGuardianFamily'])
            ->name('students.ajax.guardian-family');

        // Student Attendance (تسجيل وحالات الحضور والغياب)
        Route::get('attendance', [AcademicStudentAttendanceController::class, 'index'])
            ->name('attendance.index');
        Route::get('attendance/data', [AcademicStudentAttendanceController::class, 'data'])
            ->name('attendance.data');
        Route::get('attendance/class/{classId}/roster', [AcademicStudentAttendanceController::class, 'classRoster'])
            ->name('attendance.class-roster');
        Route::post('attendance/multi', [AcademicStudentAttendanceController::class, 'storeMulti'])
            ->name('attendance.store-multi');
        Route::post('attendance/single', [AcademicStudentAttendanceController::class, 'storeSingle'])
            ->name('attendance.store-single');
        Route::post('attendance/approve', [AcademicStudentAttendanceController::class, 'approve'])
            ->name('attendance.approve');
        Route::post('attendance/reject', [AcademicStudentAttendanceController::class, 'reject'])
            ->name('attendance.reject');
        Route::get('attendance/student/{studentId}/report', [AcademicStudentAttendanceController::class, 'studentReport'])
            ->name('attendance.student-report');
        Route::get('attendance/guardian/{guardianId}/report', [AcademicStudentAttendanceController::class, 'guardianReport'])
            ->name('attendance.guardian-report');

        // Academic Teachers (المعلمين وتسكين المراحل والفصول والمواد)
        Route::get('teachers', [AcademicTeacherController::class, 'index'])
            ->name('teachers.index');
        Route::get('teachers/data', [AcademicTeacherController::class, 'data'])
            ->name('teachers.data');
        Route::post('teachers', [AcademicTeacherController::class, 'store'])
            ->name('teachers.store');
        Route::get('teachers/{id}', [AcademicTeacherController::class, 'show'])
            ->name('teachers.show');
        Route::put('teachers/{id}', [AcademicTeacherController::class, 'update'])
            ->name('teachers.update');
        Route::delete('teachers/{id}', [AcademicTeacherController::class, 'destroy'])
            ->name('teachers.destroy');
        Route::patch('teachers/{id}/toggle-status', [AcademicTeacherController::class, 'toggleStatus'])
            ->name('teachers.toggle-status');
        Route::get('teachers/{id}/allocation', [AcademicTeacherController::class, 'getAllocation'])
            ->name('teachers.allocation');
        Route::post('teachers/{id}/allocation', [AcademicTeacherController::class, 'saveAllocation'])
            ->name('teachers.save-allocation');

        // Student Galleries / معرض صور وفيديوهات الطلاب
        Route::get('galleries', [TeacherGalleryController::class, 'index'])
            ->name('galleries.index');
        Route::get('galleries/data', [TeacherGalleryController::class, 'data'])
            ->name('galleries.data');
        Route::post('galleries', [TeacherGalleryController::class, 'store'])
            ->name('galleries.store');
        Route::get('galleries/{id}', [TeacherGalleryController::class, 'show'])
            ->name('galleries.show');
        Route::put('galleries/{id}', [TeacherGalleryController::class, 'update'])
            ->name('galleries.update');
        Route::delete('galleries/{id}', [TeacherGalleryController::class, 'destroy'])
            ->name('galleries.destroy');
        Route::delete('galleries/images/{id}', [TeacherGalleryController::class, 'deleteImage'])
            ->name('galleries.images.destroy');
        Route::get('galleries/class-students/{classroomId}', [TeacherGalleryController::class, 'getClassStudents'])
            ->name('galleries.class-students');

        // Incident Reports / تقارير الحوادث والإصابات
        Route::get('incident-reports/data', [IncidentReportController::class, 'data'])
            ->name('incident-reports.data');
        Route::get('incident-reports/{id}/print', [IncidentReportController::class, 'print'])
            ->name('incident-reports.print');
        Route::resource('incident-reports', IncidentReportController::class);

        // App Calendar / رزنامة التطبيق
        Route::get('calender/calendar-data', [CalenderController::class, 'calendarEvents'])
            ->name('calender.calendar-data');
        Route::patch('calender/{id}/toggle-status', [CalenderController::class, 'toggleStatus'])
            ->name('calender.toggle-status');
        Route::resource('calender', CalenderController::class);

        // Events / الفاعليات
        Route::get('events/data', [\Modules\Academic\Http\Controllers\EventController::class, 'data'])
            ->name('events.data');
        Route::get('events/branches/{institutionId}', [\Modules\Academic\Http\Controllers\EventController::class, 'branches'])
            ->name('events.branches');
        Route::get('events/classes/{branchId}', [\Modules\Academic\Http\Controllers\EventController::class, 'classes'])
            ->name('events.classes');
        Route::patch('events/{id}/toggle-status', [\Modules\Academic\Http\Controllers\EventController::class, 'toggleStatus'])
            ->name('events.toggle-status');
        Route::resource('events', \Modules\Academic\Http\Controllers\EventController::class);

        // Personal Calendar / الرزنامة الشخصية
        Route::get('personal-calendar/calendar-data', [\Modules\Academic\Http\Controllers\PersonalCalendarController::class, 'calendarEvents'])
            ->name('personal-calendar.calendar-data');
        Route::patch('personal-calendar/{id}/status', [\Modules\Academic\Http\Controllers\PersonalCalendarController::class, 'updateStatus'])
            ->name('personal-calendar.status');
        Route::resource('personal-calendar', \Modules\Academic\Http\Controllers\PersonalCalendarController::class);

        // Meetings Calendar / رزنامة الاجتماعات
        Route::get('meeting-calendar/calendar-data', [\Modules\Academic\Http\Controllers\MeetingCalendarController::class, 'calendarEvents'])
            ->name('meeting-calendar.calendar-data');
        Route::get('meeting-calendar/sidebar-data', [\Modules\Academic\Http\Controllers\MeetingCalendarController::class, 'sidebarData'])
            ->name('meeting-calendar.sidebar-data');
        Route::patch('meeting-calendar/{id}/status', [\Modules\Academic\Http\Controllers\MeetingCalendarController::class, 'updateStatus'])
            ->name('meeting-calendar.status');
        Route::resource('meeting-calendar', \Modules\Academic\Http\Controllers\MeetingCalendarController::class);

        // Interviews Calendar / رزنامة المقابلات
        Route::get('interviews_calender/calendar-data', [\Modules\Academic\Http\Controllers\InterviewsCalenderController::class, 'calendarEvents'])
            ->name('interviews_calender.calendar-data');
        Route::get('interviews_calender/sidebar-data', [\Modules\Academic\Http\Controllers\InterviewsCalenderController::class, 'sidebarData'])
            ->name('interviews_calender.sidebar-data');
        Route::get('interviews_calender/check-guardian', [\Modules\Academic\Http\Controllers\InterviewsCalenderController::class, 'checkGuardian'])
            ->name('interviews_calender.check-guardian');
        Route::get('interviews_calender/branches/{institutionId}', [\Modules\Academic\Http\Controllers\InterviewsCalenderController::class, 'branches'])
            ->name('interviews_calender.branches');
        Route::patch('interviews_calender/{id}/status', [\Modules\Academic\Http\Controllers\InterviewsCalenderController::class, 'updateStatus'])
            ->name('interviews_calender.status');
        Route::resource('interviews_calender', \Modules\Academic\Http\Controllers\InterviewsCalenderController::class);
    });
