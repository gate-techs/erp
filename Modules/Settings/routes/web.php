<?php

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\SettingsController;
use Modules\Settings\Http\Controllers\TranslationController;

Route::middleware(['auth'])->group(function () {
    // Company / General Settings
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');

    // Language & Translation Manager
    Route::get('settings/translations', [TranslationController::class, 'index'])->name('settings.translations.index');
    Route::put('settings/translations', [TranslationController::class, 'update'])->name('settings.translations.update');
    Route::post('settings/translations/key', [TranslationController::class, 'store'])->name('settings.translations.store');
    Route::delete('settings/translations/key', [TranslationController::class, 'destroy'])->name('settings.translations.destroy');
});
