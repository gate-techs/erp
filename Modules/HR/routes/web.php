<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\AttendanceController;
use Modules\HR\Http\Controllers\EmployeeController;
use Modules\HR\Http\Controllers\HRSettingController;
use Modules\HR\Http\Controllers\HrGeneralSettingController;
use Modules\HR\Http\Controllers\JobTitleController;
use Modules\HR\Http\Controllers\ShiftController;
use Modules\HR\Http\Controllers\WorkScheduleController;
use Modules\HR\Http\Controllers\OfficialHolidayController;
use Modules\HR\Http\Controllers\WorkPolicyController;
use Modules\HR\Http\Controllers\LeaveRequestController;
use Modules\HR\Http\Controllers\PermissionRequestController;
use Modules\HR\Http\Controllers\LoanController;
use Modules\HR\Http\Controllers\RewardController;
use Modules\HR\Http\Controllers\PenaltyController;
use Modules\HR\Http\Controllers\PayrollController;
use Modules\HR\Http\Controllers\WorkforcePlanController;
use Modules\HR\Http\Controllers\JobRequisitionController;
use Modules\HR\Http\Controllers\JobOpeningController;
use Modules\HR\Http\Controllers\JobApplicationController;
use Modules\HR\Http\Controllers\InterviewController;
use Modules\HR\Http\Controllers\JobOfferController;
use Modules\HR\Http\Controllers\OnboardingController;
use Modules\HR\Http\Controllers\HRDashboardController;
use Modules\HR\Http\Controllers\AttendanceReportController;
use Modules\HR\Http\Controllers\LeaveReportController;
use Modules\HR\Http\Controllers\PayrollReportController;
use Modules\HR\Http\Controllers\EmployeeReportController;

Route::middleware(['auth', 'verified'])->prefix('hr')->name('hr.')->group(function () {
    // Dashboard & Command Center (الرئيسية ولوحة تحكم الموارد البشرية)
    Route::get('/', [HRDashboardController::class, 'index'])->name('dashboard');

    // HR Reports Hub (تقارير الموارد البشرية)
    Route::prefix('reports')->name('reports.')->group(function () {
        // 1. Attendance & Time Tracking Reports (تقارير الحضور والدوام)
        Route::get('attendance', [AttendanceReportController::class, 'index'])->name('attendance.index');
        Route::get('attendance/print', [AttendanceReportController::class, 'print'])->name('attendance.print');
        Route::get('attendance/export', [AttendanceReportController::class, 'export'])->name('attendance.export');

        // 2. Leaves & Permissions Reports (تقارير الإجازات والاستئذانات)
        Route::get('leaves', [LeaveReportController::class, 'index'])->name('leaves.index');
        Route::get('leaves/print', [LeaveReportController::class, 'print'])->name('leaves.print');
        Route::get('leaves/export', [LeaveReportController::class, 'export'])->name('leaves.export');

        // 3. Payroll & Compensation Reports (تقارير الرواتب والمالية)
        Route::get('payrolls', [PayrollReportController::class, 'index'])->name('payrolls.index');
        Route::get('payrolls/print', [PayrollReportController::class, 'print'])->name('payrolls.print');
        Route::get('payrolls/payslip/{itemId}', [PayrollReportController::class, 'payslip'])->name('payrolls.payslip');
        Route::get('payrolls/export', [PayrollReportController::class, 'export'])->name('payrolls.export');

        // 4. Employee Master & Documents Reports (تقارير بيانات وملفات الموظفين)
        Route::get('employees', [EmployeeReportController::class, 'index'])->name('employees.index');
        Route::get('employees/print', [EmployeeReportController::class, 'print'])->name('employees.print');
        Route::get('employees/export', [EmployeeReportController::class, 'export'])->name('employees.export');
    });

    // Payroll & Wage Management (إدارة مسير الرواتب والأجور)
    Route::get('payrolls/data', [PayrollController::class, 'data'])->name('payrolls.data');
    Route::get('payrolls/{id}/items-data', [PayrollController::class, 'itemsData'])->name('payrolls.items-data');
    Route::get('payrolls/{id}/items/{itemId}', [PayrollController::class, 'getItem'])->name('payrolls.items.get');
    Route::put('payrolls/{id}/items/{itemId}', [PayrollController::class, 'updateItem'])->name('payrolls.items.update');
    Route::get('payrolls/{id}/items/{itemId}/payslip', [PayrollController::class, 'payslip'])->name('payrolls.items.payslip');
    Route::post('payrolls/{id}/recalculate', [PayrollController::class, 'recalculate'])->name('payrolls.recalculate');
    Route::post('payrolls/{id}/approve', [PayrollController::class, 'approve'])->name('payrolls.approve');
    Route::post('payrolls/{id}/disburse', [PayrollController::class, 'disburse'])->name('payrolls.disburse');
    Route::post('payrolls/{id}/cancel', [PayrollController::class, 'cancel'])->name('payrolls.cancel');
    Route::get('payrolls/{id}/export-wps', [PayrollController::class, 'exportWps'])->name('payrolls.export-wps');
    Route::resource('payrolls', PayrollController::class)->except(['create', 'edit', 'update']);

    // Rewards & Incentives Management (إدارة المكافآت والحوافز)
    Route::get('rewards/data', [RewardController::class, 'data'])->name('rewards.data');
    Route::get('rewards/calculate', [RewardController::class, 'calculate'])->name('rewards.calculate');
    Route::post('rewards/{id}/approve', [RewardController::class, 'approve'])->name('rewards.approve');
    Route::post('rewards/{id}/pay', [RewardController::class, 'pay'])->name('rewards.pay');
    Route::post('rewards/{id}/reject', [RewardController::class, 'reject'])->name('rewards.reject');
    Route::resource('rewards', RewardController::class)->except(['create', 'edit']);

    // Penalties & Disciplinary Actions Management (إدارة الجزاءات والمخالفات)
    Route::get('penalties/data', [PenaltyController::class, 'data'])->name('penalties.data');
    Route::get('penalties/calculate', [PenaltyController::class, 'calculate'])->name('penalties.calculate');
    Route::post('penalties/{id}/approve', [PenaltyController::class, 'approve'])->name('penalties.approve');
    Route::post('penalties/{id}/deduct', [PenaltyController::class, 'deduct'])->name('penalties.deduct');
    Route::post('penalties/{id}/cancel', [PenaltyController::class, 'cancel'])->name('penalties.cancel');
    Route::resource('penalties', PenaltyController::class)->except(['create', 'edit']);

    // Loans & Advances Management (إدارة السلف والقروض)
    Route::get('loans/data', [LoanController::class, 'data'])->name('loans.data');
    Route::get('loans/check-eligibility', [LoanController::class, 'checkEligibility'])->name('loans.check-eligibility');
    Route::get('loans/preview-schedule', [LoanController::class, 'previewSchedule'])->name('loans.preview-schedule');
    Route::post('loans/{id}/approve', [LoanController::class, 'approve'])->name('loans.approve');
    Route::post('loans/{id}/disburse', [LoanController::class, 'disburse'])->name('loans.disburse');
    Route::post('loans/{id}/reject', [LoanController::class, 'reject'])->name('loans.reject');
    Route::post('loans/installments/{id}/postpone', [LoanController::class, 'postponeInstallment'])->name('loans.installments.postpone');
    Route::post('loans/installments/{id}/pay', [LoanController::class, 'payInstallment'])->name('loans.installments.pay');
    Route::resource('loans', LoanController::class)->except(['create', 'edit']);

    // Permission Requests Management (إدارة طلبات الأذونات والمغادرات)
    Route::get('permissions/data', [PermissionRequestController::class, 'data'])->name('permissions.data');
    Route::get('permissions/check-balance', [PermissionRequestController::class, 'checkBalance'])->name('permissions.check-balance');
    Route::post('permissions/{id}/approve', [PermissionRequestController::class, 'approve'])->name('permissions.approve');
    Route::post('permissions/{id}/reject', [PermissionRequestController::class, 'reject'])->name('permissions.reject');
    Route::resource('permissions', PermissionRequestController::class)->except(['create', 'edit']);

    // Leave Requests Management (إدارة طلبات الإجازات)
    Route::get('leaves/data', [LeaveRequestController::class, 'data'])->name('leaves.data');
    Route::get('leaves/check-balance', [LeaveRequestController::class, 'checkBalance'])->name('leaves.check-balance');
    Route::post('leaves/{id}/approve', [LeaveRequestController::class, 'approve'])->name('leaves.approve');
    Route::post('leaves/{id}/reject', [LeaveRequestController::class, 'reject'])->name('leaves.reject');
    Route::resource('leaves', LeaveRequestController::class)->except(['create', 'edit']);

    // Attendance & Absence Management (إدارة الحضور والإنصراف)
    Route::get('attendances/data', [AttendanceController::class, 'data'])->name('attendances.data');
    Route::get('attendances/matrix-data', [AttendanceController::class, 'matrixData'])->name('attendances.matrix-data');
    Route::post('attendances/record-punch', [AttendanceController::class, 'recordPunch'])->name('attendances.record-punch');
    Route::post('attendances/import-excel', [AttendanceController::class, 'importExcel'])->name('attendances.import-excel');
    Route::get('attendances/download-template', [AttendanceController::class, 'downloadTemplate'])->name('attendances.download-template');
    Route::resource('attendances', AttendanceController::class)->only(['index', 'store', 'update', 'destroy']);

    // Employees Management (إدارة الموظفين)
    Route::get('employees/data', [EmployeeController::class, 'data'])->name('employees.data');
    Route::resource('employees', EmployeeController::class);

    // Workforce Planning (تخطيط القوى العاملة)
    Route::get('workforce-plans/data', [WorkforcePlanController::class, 'data'])->name('workforce-plans.data');
    Route::get('workforce-plans/live-headcount', [WorkforcePlanController::class, 'getLiveHeadcount'])->name('workforce-plans.live-headcount');
    Route::post('workforce-plans/{id}/submit', [WorkforcePlanController::class, 'submit'])->name('workforce-plans.submit');
    Route::post('workforce-plans/{id}/approve', [WorkforcePlanController::class, 'approve'])->name('workforce-plans.approve');
    Route::post('workforce-plans/{id}/activate', [WorkforcePlanController::class, 'activate'])->name('workforce-plans.activate');
    Route::post('workforce-plans/{id}/close', [WorkforcePlanController::class, 'close'])->name('workforce-plans.close');
    Route::post('workforce-plans/{id}/recalculate', [WorkforcePlanController::class, 'recalculateHeadcount'])->name('workforce-plans.recalculate');
    Route::resource('workforce-plans', WorkforcePlanController::class);

    // Job Requisitions (طلبات التوظيف من الأقسام)
    Route::get('job-requisitions/data', [JobRequisitionController::class, 'data'])->name('job-requisitions.data');
    Route::get('job-requisitions/plan-items', [JobRequisitionController::class, 'getPlanItems'])->name('job-requisitions.plan-items');
    Route::post('job-requisitions/{id}/submit', [JobRequisitionController::class, 'submit'])->name('job-requisitions.submit');
    Route::post('job-requisitions/{id}/approve', [JobRequisitionController::class, 'approve'])->name('job-requisitions.approve');
    Route::post('job-requisitions/{id}/reject', [JobRequisitionController::class, 'reject'])->name('job-requisitions.reject');
    Route::post('job-requisitions/{id}/start-recruitment', [JobRequisitionController::class, 'startRecruitment'])->name('job-requisitions.start-recruitment');
    Route::post('job-requisitions/{id}/cancel', [JobRequisitionController::class, 'cancel'])->name('job-requisitions.cancel');
    Route::resource('job-requisitions', JobRequisitionController::class);

    // Job Openings & Postings (إعلانات الشواغر الوظيفية)
    Route::get('job-openings/data', [JobOpeningController::class, 'data'])->name('job-openings.data');
    Route::get('job-openings/requisition-details', [JobOpeningController::class, 'requisitionDetails'])->name('job-openings.requisition-details');
    Route::post('job-openings/{id}/publish', [JobOpeningController::class, 'publish'])->name('job-openings.publish');
    Route::post('job-openings/{id}/close', [JobOpeningController::class, 'close'])->name('job-openings.close');
    Route::resource('job-openings', JobOpeningController::class);

    // Candidate Pipeline & Applications (استقبال وتتبع المرشحين)
    Route::get('job-applications/data', [JobApplicationController::class, 'data'])->name('job-applications.data');
    Route::post('job-applications/{id}/stage', [JobApplicationController::class, 'updateStage'])->name('job-applications.update-stage');
    Route::post('job-applications/{id}/reject', [JobApplicationController::class, 'reject'])->name('job-applications.reject');
    Route::post('job-applications/{id}/reopen', [JobApplicationController::class, 'reopen'])->name('job-applications.reopen');
    Route::post('job-applications/{id}/rating', [JobApplicationController::class, 'updateRating'])->name('job-applications.update-rating');
    Route::get('job-applications/{id}/resume', [JobApplicationController::class, 'downloadResume'])->name('job-applications.download-resume');
    Route::resource('job-applications', JobApplicationController::class);

    // Interviews & Candidate Evaluation (إدارة المقابلات وبطاقات التقييم)
    Route::get('interviews/data', [InterviewController::class, 'data'])->name('interviews.data');
    Route::get('interviews/{id}/evaluate', [InterviewController::class, 'evaluate'])->name('interviews.evaluate');
    Route::post('interviews/{id}/evaluate', [InterviewController::class, 'storeEvaluation'])->name('interviews.store-evaluation');
    Route::post('interviews/{id}/cancel', [InterviewController::class, 'cancel'])->name('interviews.cancel');
    Route::resource('interviews', InterviewController::class);

    // Job Offers Management (إدارة عروض العمل وخطابات التوظيف)
    Route::get('job-offers/data', [JobOfferController::class, 'data'])->name('job-offers.data');
    Route::post('job-offers/{id}/submit', [JobOfferController::class, 'submitForApproval'])->name('job-offers.submit');
    Route::post('job-offers/{id}/approve', [JobOfferController::class, 'approve'])->name('job-offers.approve');
    Route::post('job-offers/{id}/send', [JobOfferController::class, 'send'])->name('job-offers.send');
    Route::post('job-offers/{id}/accept', [JobOfferController::class, 'accept'])->name('job-offers.accept');
    Route::post('job-offers/{id}/decline', [JobOfferController::class, 'decline'])->name('job-offers.decline');
    Route::post('job-offers/{id}/cancel', [JobOfferController::class, 'cancel'])->name('job-offers.cancel');
    Route::resource('job-offers', JobOfferController::class);

    // Onboarding & Candidate Conversion (التهيئة واستقبال الموظف الجديد)
    Route::get('onboardings/data', [OnboardingController::class, 'data'])->name('onboardings.data');
    Route::post('onboardings/{id}/tasks', [OnboardingController::class, 'addTask'])->name('onboardings.tasks.store');
    Route::post('onboardings/tasks/{taskId}/toggle', [OnboardingController::class, 'toggleTask'])->name('onboardings.tasks.toggle');
    Route::post('onboardings/{id}/convert', [OnboardingController::class, 'convertToEmployee'])->name('onboardings.convert');
    Route::resource('onboardings', OnboardingController::class)->except(['edit', 'update']);

    // HR Settings Root (Redirects to administrations)
    Route::get('settings', [HRSettingController::class, 'index'])->name('settings');

    Route::prefix('settings')->name('settings.')->group(function () {
        // 1. Administrations (الإدارات)
        Route::get('administrations/data', [HRSettingController::class, 'administrationsData'])->name('administrations.data');
        Route::get('administrations', [HRSettingController::class, 'administrations'])->name('administrations.index');
        Route::post('administrations', [HRSettingController::class, 'storeAdministration'])->name('administrations.store');
        Route::put('administrations/{id}', [HRSettingController::class, 'updateAdministration'])->name('administrations.update');
        Route::delete('administrations/{id}', [HRSettingController::class, 'destroyAdministration'])->name('administrations.destroy');

        // 2. Departments (الأقسام)
        Route::get('departments/data', [HRSettingController::class, 'departmentsData'])->name('departments.data');
        Route::get('departments', [HRSettingController::class, 'departments'])->name('departments.index');
        Route::post('departments', [HRSettingController::class, 'storeDepartment'])->name('departments.store');
        Route::put('departments/{id}', [HRSettingController::class, 'updateDepartment'])->name('departments.update');
        Route::delete('departments/{id}', [HRSettingController::class, 'destroyDepartment'])->name('departments.destroy');

        // 3. Job Titles (المسميات الوظيفية)
        Route::get('job-titles/data', [JobTitleController::class, 'data'])->name('job-titles.data');
        Route::get('job-titles', [JobTitleController::class, 'index'])->name('job-titles.index');
        Route::post('job-titles', [JobTitleController::class, 'store'])->name('job-titles.store');
        Route::put('job-titles/{id}', [JobTitleController::class, 'update'])->name('job-titles.update');
        Route::delete('job-titles/{id}', [JobTitleController::class, 'destroy'])->name('job-titles.destroy');

        // 4. Allowance Types (أنواع البدلات)
        Route::get('allowance-types/data', [HrGeneralSettingController::class, 'allowanceTypesData'])->name('allowance-types.data');
        Route::get('allowance-types', [HrGeneralSettingController::class, 'allowanceTypes'])->name('allowance-types.index');
        Route::post('allowance-types', [HrGeneralSettingController::class, 'storeAllowanceType'])->name('allowance-types.store');
        Route::put('allowance-types/{id}', [HrGeneralSettingController::class, 'updateAllowanceType'])->name('allowance-types.update');
        Route::delete('allowance-types/{id}', [HrGeneralSettingController::class, 'destroyAllowanceType'])->name('allowance-types.destroy');

        // 5. Document Types (أنواع المستندات)
        Route::get('document-types/data', [HrGeneralSettingController::class, 'documentTypesData'])->name('document-types.data');
        Route::get('document-types', [HrGeneralSettingController::class, 'documentTypes'])->name('document-types.index');
        Route::post('document-types', [HrGeneralSettingController::class, 'storeDocumentType'])->name('document-types.store');
        Route::put('document-types/{id}', [HrGeneralSettingController::class, 'updateDocumentType'])->name('document-types.update');
        Route::delete('document-types/{id}', [HrGeneralSettingController::class, 'destroyDocumentType'])->name('document-types.destroy');

        // 5.1 Reward Types (أنواع المكافآت والحوافز)
        Route::get('reward-types/data', [HrGeneralSettingController::class, 'rewardTypesData'])->name('reward-types.data');
        Route::get('reward-types', [HrGeneralSettingController::class, 'rewardTypes'])->name('reward-types.index');
        Route::post('reward-types', [HrGeneralSettingController::class, 'storeRewardType'])->name('reward-types.store');
        Route::put('reward-types/{id}', [HrGeneralSettingController::class, 'updateRewardType'])->name('reward-types.update');
        Route::delete('reward-types/{id}', [HrGeneralSettingController::class, 'destroyRewardType'])->name('reward-types.destroy');

        // 5.2 Penalty Types (أنواع الجزاءات والمخالفات)
        Route::get('penalty-types/data', [HrGeneralSettingController::class, 'penaltyTypesData'])->name('penalty-types.data');
        Route::get('penalty-types', [HrGeneralSettingController::class, 'penaltyTypes'])->name('penalty-types.index');
        Route::post('penalty-types', [HrGeneralSettingController::class, 'storePenaltyType'])->name('penalty-types.store');
        Route::put('penalty-types/{id}', [HrGeneralSettingController::class, 'updatePenaltyType'])->name('penalty-types.update');
        Route::delete('penalty-types/{id}', [HrGeneralSettingController::class, 'destroyPenaltyType'])->name('penalty-types.destroy');

        // 6. Shifts (الورديات)
        Route::get('shifts/data', [ShiftController::class, 'data'])->name('shifts.data');
        Route::get('shifts', [ShiftController::class, 'index'])->name('shifts.index');
        Route::post('shifts', [ShiftController::class, 'store'])->name('shifts.store');
        Route::put('shifts/{id}', [ShiftController::class, 'update'])->name('shifts.update');
        Route::delete('shifts/{id}', [ShiftController::class, 'destroy'])->name('shifts.destroy');

        // 7. Work Schedules (جداول العمل)
        Route::get('work-schedules/data', [WorkScheduleController::class, 'data'])->name('work-schedules.data');
        Route::get('work-schedules', [WorkScheduleController::class, 'index'])->name('work-schedules.index');
        Route::post('work-schedules', [WorkScheduleController::class, 'store'])->name('work-schedules.store');
        Route::put('work-schedules/{id}', [WorkScheduleController::class, 'update'])->name('work-schedules.update');
        Route::delete('work-schedules/{id}', [WorkScheduleController::class, 'destroy'])->name('work-schedules.destroy');

        // 8. Official Holidays (العطلات الرسمية)
        Route::get('official-holidays/data', [OfficialHolidayController::class, 'data'])->name('official-holidays.data');
        Route::get('official-holidays', [OfficialHolidayController::class, 'index'])->name('official-holidays.index');
        Route::post('official-holidays', [OfficialHolidayController::class, 'store'])->name('official-holidays.store');
        Route::put('official-holidays/{id}', [OfficialHolidayController::class, 'update'])->name('official-holidays.update');
        Route::delete('official-holidays/{id}', [OfficialHolidayController::class, 'destroy'])->name('official-holidays.destroy');

        // 9. Work Policies & Rules (سياسات العمل واللوائح)
        Route::get('policies', [WorkPolicyController::class, 'index'])->name('policies.index');
        Route::post('policies', [WorkPolicyController::class, 'update'])->name('policies.update');
        Route::post('policies/reset', [WorkPolicyController::class, 'resetDefaults'])->name('policies.reset');
    });
});
