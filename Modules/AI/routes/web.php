<?php

use Illuminate\Support\Facades\Route;
use Modules\AI\Http\Controllers\AISettingsController;

Route::middleware(['auth'])->prefix('settings/ai')->name('settings.ai.')->group(function () {
    Route::get('/', [AISettingsController::class, 'index'])->name('index');
    Route::post('providers', [AISettingsController::class, 'storeProvider'])->name('providers.store');
    Route::put('providers/{provider}', [AISettingsController::class, 'updateProvider'])->name('providers.update');
    Route::delete('providers/{provider}', [AISettingsController::class, 'destroyProvider'])->name('providers.destroy');
    Route::post('tasks/{task}/providers', [AISettingsController::class, 'assignProvider'])->name('tasks.providers.assign');
    Route::delete('tasks/{task}/providers/{provider}', [AISettingsController::class, 'removeAssignment'])->name('tasks.providers.remove');
});

Route::middleware(['auth'])->prefix('ai')->name('ai.')->group(function () {
    Route::post('translate-name', [\Modules\AI\Http\Controllers\AIController::class, 'translateName'])->name('translate-name');
    Route::post('translate', [\Modules\AI\Http\Controllers\AIController::class, 'translate'])->name('translate');
    Route::post('generate', [\Modules\AI\Http\Controllers\AIController::class, 'generate'])->name('generate');
});
