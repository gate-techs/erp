<?php

use Illuminate\Support\Facades\Route;
use Modules\AuditLog\Http\Controllers\AuditLogController;
use Modules\AuditLog\Http\Controllers\AuditLogSettingsController;

Route::middleware(['auth'])->prefix('audit-logs')->name('audit-logs.')->group(function () {
    Route::get('/', [AuditLogController::class, 'index'])->name('index');
    Route::get('/data', [AuditLogController::class, 'data'])->name('data');

    // External DB & Module Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [AuditLogSettingsController::class, 'index'])->name('index');
        Route::post('/', [AuditLogSettingsController::class, 'update'])->name('update');
        Route::post('/test-connection', [AuditLogSettingsController::class, 'testConnection'])->name('test-connection');
        Route::post('/sync-schema', [AuditLogSettingsController::class, 'syncSchema'])->name('sync-schema');
    });

    Route::get('/{id}', [AuditLogController::class, 'show'])->name('show');
});
