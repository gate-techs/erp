<?php

use Illuminate\Support\Facades\Route;
use Modules\Settings\Http\Controllers\Api\SettingApiController;

Route::prefix('v1')->group(function () {
    // Public settings for mobile apps (logo, site name, contact, currency)
    Route::get('settings', [SettingApiController::class, 'index'])->name('api.settings.index');

    // Admin updates via API
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::put('settings', [SettingApiController::class, 'update'])->name('api.settings.update');
    });
});
