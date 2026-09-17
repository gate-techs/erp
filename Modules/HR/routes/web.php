<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\EmployeeController;
use Modules\HR\Http\Controllers\HRSettingController;
use Modules\HR\Http\Controllers\HrGeneralSettingController;
use Modules\HR\Http\Controllers\JobTitleController;
use Modules\HR\Http\Controllers\ShiftController;
use Modules\HR\Http\Controllers\WorkScheduleController;
use Modules\HR\Http\Controllers\OfficialHolidayController;

Route::middleware(['auth', 'verified'])->prefix('hr')->name('hr.')->group(function () {
    // Employees Management (إدارة الموظفين)
    Route::get('employees/data', [EmployeeController::class, 'data'])->name('employees.data');
    Route::resource('employees', EmployeeController::class);

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
    });
});
