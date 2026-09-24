<?php

use Illuminate\Support\Facades\Route;
use Modules\Accounting\Http\Controllers\AccountingController;
use Modules\Accounting\Http\Controllers\AccountController;
use Modules\Accounting\Http\Controllers\EntityController;
use Modules\Accounting\Http\Controllers\FiscalYearController;
use Modules\Accounting\Http\Controllers\LockDateController;

Route::middleware(['auth', 'verified'])->prefix('accounting')->as('accounting.')->group(function () {
    // Dashboard / Overview
    Route::get('/', [AccountingController::class, 'index'])->name('index');

    // Entities (الكيانات المحاسبية)
    Route::get('entities/data', [EntityController::class, 'data'])->name('entities.data');
    Route::resource('entities', EntityController::class);

    // Fiscal Years
    Route::get('fiscal-years/data', [FiscalYearController::class, 'data'])->name('fiscal-years.data');
    Route::post('fiscal-years/{id}/toggle-status', [FiscalYearController::class, 'toggleStatus'])->name('fiscal-years.toggle-status');
    Route::resource('fiscal-years', FiscalYearController::class);

    // Lock Dates and Exceptions
    Route::put('lock-dates/{entityId}', [LockDateController::class, 'update'])->name('lock-dates.update');
    Route::post('lock-dates/exceptions', [LockDateController::class, 'storeException'])->name('lock-dates.exceptions.store');
    Route::post('lock-dates/exceptions/{id}/revoke', [LockDateController::class, 'revokeException'])->name('lock-dates.exceptions.revoke');
    Route::get('lock-dates', [LockDateController::class, 'index'])->name('lock-dates.index');

    // Cost Centers (مراكز التكلفة)
    Route::get('cost-centers/data', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'data'])->name('cost-centers.data');
    Route::get('cost-centers/parents', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'getParentsByEntity'])->name('cost-centers.parents');
    Route::get('cost-centers/branches', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'getBranchesByEntity'])->name('cost-centers.branches');
    Route::post('cost-centers/{id}/toggle-status', [\Modules\Accounting\Http\Controllers\CostCenterController::class, 'toggleStatus'])->name('cost-centers.toggle-status');
    Route::resource('cost-centers', \Modules\Accounting\Http\Controllers\CostCenterController::class);


    // Chart of Accounts (دليل / شجرة الحسابات)
    Route::get('accounts/template/download', [AccountController::class, 'downloadTemplate'])->name('accounts.template.download');
    Route::get('accounts/export', [AccountController::class, 'export'])->name('accounts.export');
    Route::post('accounts/import', [AccountController::class, 'import'])->name('accounts.import');
    Route::get('accounts/data', [AccountController::class, 'data'])->name('accounts.data');
    Route::get('accounts/tree-data', [AccountController::class, 'treeData'])->name('accounts.tree-data');
    Route::get('accounts/suggest-code', [AccountController::class, 'suggestCode'])->name('accounts.suggest-code');
    Route::get('accounts/parents', [AccountController::class, 'parents'])->name('accounts.parents');
    Route::post('accounts/{account}/toggle-status', [AccountController::class, 'toggleStatus'])->name('accounts.toggle-status');
    Route::post('accounts/{account}/toggle-deprecated', [AccountController::class, 'toggleDeprecated'])->name('accounts.toggle-deprecated');
    Route::resource('accounts', AccountController::class);
});
