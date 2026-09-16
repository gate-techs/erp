<?php

use Illuminate\Support\Facades\Route;
use Modules\Nursery\Http\Controllers\NurseryController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('nurseries', NurseryController::class)->names('nursery');
});
