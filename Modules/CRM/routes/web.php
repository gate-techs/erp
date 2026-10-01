<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Http\Controllers\LeadController;
use Modules\CRM\Http\Controllers\ServiceRequestController;

Route::middleware(['auth', 'verified'])->prefix('crm')->name('crm.')->group(function () {
    // 1. Leads Management (العملاء المحتملين)
    Route::middleware('permission:crm.leads.view')->group(function () {
        Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
        Route::get('leads/data', [LeadController::class, 'data'])->name('leads.data');
    });
    Route::post('leads', [LeadController::class, 'store'])
        ->middleware('permission:crm.leads.create')
        ->name('leads.store');
    Route::match(['put', 'patch'], 'leads/{lead}', [LeadController::class, 'update'])
        ->middleware('permission:crm.leads.edit')
        ->name('leads.update');
    Route::delete('leads/{lead}', [LeadController::class, 'destroy'])
        ->middleware('permission:crm.leads.delete')
        ->name('leads.destroy');

    // 2. Service Requests & Complaints (طلبات الخدمة والشكاوى)
    Route::middleware('permission:crm.service-requests.view')->group(function () {
        Route::get('service-requests', [ServiceRequestController::class, 'index'])->name('service-requests.index');
        Route::get('service-requests/data', [ServiceRequestController::class, 'data'])->name('service-requests.data');
        Route::get('service-requests/search-civil-id', [ServiceRequestController::class, 'searchCivilId'])->name('service-requests.search-civil-id');
        Route::get('service-requests/{service_request}', [ServiceRequestController::class, 'show'])->name('service-requests.show');
    });
    Route::post('service-requests', [ServiceRequestController::class, 'store'])
        ->middleware('permission:crm.service-requests.create')
        ->name('service-requests.store');
    Route::match(['put', 'patch'], 'service-requests/{service_request}', [ServiceRequestController::class, 'update'])
        ->middleware('permission:crm.service-requests.edit')
        ->name('service-requests.update');
    Route::delete('service-requests/{service_request}', [ServiceRequestController::class, 'destroy'])
        ->middleware('permission:crm.service-requests.delete')
        ->name('service-requests.destroy');
});
