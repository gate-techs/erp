<?php

use Illuminate\Support\Facades\Route;
use Modules\EmployeePortal\Http\Controllers\EmployeePortalController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('employeeportals', EmployeePortalController::class)->names('employeeportal');
});
