<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Http\Controllers\LeadController;
use Modules\CRM\Http\Controllers\ServiceRequestController;

Route::middleware(['auth', 'verified'])->prefix('crm')->name('crm.')->group(function () {
    Route::get('leads/data', [LeadController::class, 'data'])->name('leads.data');
    Route::resource('leads', LeadController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('service-requests/data', [ServiceRequestController::class, 'data'])->name('service-requests.data');
    Route::resource('service-requests', ServiceRequestController::class)->only(['index', 'store', 'update', 'destroy']);
});
