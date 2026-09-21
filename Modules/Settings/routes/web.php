<?php

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\GeneralSettingController;
use Modules\Settings\Http\Controllers\SettingsController;
use Modules\Settings\Http\Controllers\TranslationController;

Route::middleware(['auth'])->group(function () {
    // Company / General Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

    // Language & Translation Manager
    Route::get('settings/translations', [TranslationController::class, 'index'])->name('settings.translations.index');
    Route::get('settings/translations/data', [TranslationController::class, 'data'])->name('settings.translations.data');
    Route::put('settings/translations', [TranslationController::class, 'update'])->name('settings.translations.update');
    Route::put('settings/translations/key', [TranslationController::class, 'updateKey'])->name('settings.translations.update-key');
    Route::post('settings/translations/key', [TranslationController::class, 'store'])->name('settings.translations.store');
    Route::delete('settings/translations/key', [TranslationController::class, 'destroy'])->name('settings.translations.destroy');

    // Print Templates Manager
    Route::prefix('settings/print-templates')->name('settings.print-templates.')->group(function () {
        Route::get('/', [\Modules\Settings\Http\Controllers\PrintTemplateController::class, 'index'])->name('index');
        Route::get('/{id}/edit', [\Modules\Settings\Http\Controllers\PrintTemplateController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\Modules\Settings\Http\Controllers\PrintTemplateController::class, 'update'])->name('update');
        Route::get('/{id}/preview', [\Modules\Settings\Http\Controllers\PrintTemplateController::class, 'preview'])->name('preview');
        Route::patch('/{id}/toggle-status', [\Modules\Settings\Http\Controllers\PrintTemplateController::class, 'toggleStatus'])->name('toggle-status');
    });

    // General Settings Root
    Route::get('settings/general', [GeneralSettingController::class, 'index'])->name('settings.general');

    Route::prefix('settings/general')->name('settings.general.')->group(function () {
        // 1. Governments
        Route::get('governments/data', [GeneralSettingController::class, 'governmentsData'])->name('governments.data');
        Route::get('governments', [GeneralSettingController::class, 'governments'])->name('governments.index');
        Route::post('governments', [GeneralSettingController::class, 'storeGovernment'])->name('governments.store');
        Route::put('governments/{id}', [GeneralSettingController::class, 'updateGovernment'])->name('governments.update');
        Route::delete('governments/{id}', [GeneralSettingController::class, 'destroyGovernment'])->name('governments.destroy');

        // 2. Nationalities
        Route::get('nationalities/data', [GeneralSettingController::class, 'nationalitiesData'])->name('nationalities.data');
        Route::get('nationalities', [GeneralSettingController::class, 'nationalities'])->name('nationalities.index');
        Route::post('nationalities', [GeneralSettingController::class, 'storeNationality'])->name('nationalities.store');
        Route::put('nationalities/{id}', [GeneralSettingController::class, 'updateNationality'])->name('nationalities.update');
        Route::delete('nationalities/{id}', [GeneralSettingController::class, 'destroyNationality'])->name('nationalities.destroy');

        // 3. Religions
        Route::get('religions/data', [GeneralSettingController::class, 'religionsData'])->name('religions.data');
        Route::get('religions', [GeneralSettingController::class, 'religions'])->name('religions.index');
        Route::post('religions', [GeneralSettingController::class, 'storeReligion'])->name('religions.store');
        Route::put('religions/{id}', [GeneralSettingController::class, 'updateReligion'])->name('religions.update');
        Route::delete('religions/{id}', [GeneralSettingController::class, 'destroyReligion'])->name('religions.destroy');

        // 4. Marital Statuses
        Route::get('marital-statuses/data', [GeneralSettingController::class, 'maritalStatusesData'])->name('marital-statuses.data');
        Route::get('marital-statuses', [GeneralSettingController::class, 'maritalStatuses'])->name('marital-statuses.index');
        Route::post('marital-statuses', [GeneralSettingController::class, 'storeMaritalStatus'])->name('marital-statuses.store');
        Route::put('marital-statuses/{id}', [GeneralSettingController::class, 'updateMaritalStatus'])->name('marital-statuses.update');
        Route::delete('marital-statuses/{id}', [GeneralSettingController::class, 'destroyMaritalStatus'])->name('marital-statuses.destroy');

        // 5. Banks (البنوك الرئيسية)
        Route::get('banks/data', [\Modules\Settings\Http\Controllers\BankController::class, 'data'])->name('banks.data');
        Route::get('banks', [\Modules\Settings\Http\Controllers\BankController::class, 'index'])->name('banks.index');
        Route::post('banks', [\Modules\Settings\Http\Controllers\BankController::class, 'store'])->name('banks.store');
        Route::put('banks/{id}', [\Modules\Settings\Http\Controllers\BankController::class, 'update'])->name('banks.update');
        Route::delete('banks/{id}', [\Modules\Settings\Http\Controllers\BankController::class, 'destroy'])->name('banks.destroy');

        // 6. Bank Branches (فروع البنوك)
        Route::get('bank-branches/data', [\Modules\Settings\Http\Controllers\BankController::class, 'branchesData'])->name('bank-branches.data');
        Route::get('bank-branches', [\Modules\Settings\Http\Controllers\BankController::class, 'branches'])->name('bank-branches.index');
        Route::post('bank-branches', [\Modules\Settings\Http\Controllers\BankController::class, 'storeBranch'])->name('bank-branches.store');
        Route::put('bank-branches/{id}', [\Modules\Settings\Http\Controllers\BankController::class, 'updateBranch'])->name('bank-branches.update');
        Route::delete('bank-branches/{id}', [\Modules\Settings\Http\Controllers\BankController::class, 'destroyBranch'])->name('bank-branches.destroy');
    });
});
