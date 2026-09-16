<?php

use Illuminate\Support\Facades\Route;
use Modules\HR\Http\Controllers\HRSettingController;
use Modules\HR\Http\Controllers\JobTitleController;

Route::middleware(['auth', 'verified'])->prefix('hr')->name('hr.')->group(function () {
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
    });
});
