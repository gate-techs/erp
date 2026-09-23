<?php

use Illuminate\Support\Facades\Route;
use Modules\AI\Http\Controllers\AIController;

Route::middleware(['auth:sanctum'])->prefix('v1/ai')->name('ai.')->group(function () {
    Route::post('translate', [AIController::class, 'translate'])->name('translate');
    Route::post('generate', [AIController::class, 'generate'])->name('generate');
});
