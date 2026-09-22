<?php

use Illuminate\Support\Facades\Route;
use Modules\Accounting\Http\Controllers\AccountingController;
use Modules\Accounting\Http\Controllers\AccountController;
use Modules\Accounting\Http\Controllers\EntityController;

Route::middleware(['auth', 'verified'])->prefix('accounting')->as('accounting.')->group(function () {
    // Dashboard / Overview
    Route::get('/', [AccountingController::class, 'index'])->name('index');

    // Entities (الكيانات المحاسبية)
    Route::get('entities/data', [EntityController::class, 'data'])->name('entities.data');
    Route::resource('entities', EntityController::class);

    // Chart of Accounts (دليل / شجرة الحسابات)
    Route::get('accounts/data', [AccountController::class, 'data'])->name('accounts.data');
    Route::get('accounts/tree-data', [AccountController::class, 'treeData'])->name('accounts.tree-data');
    Route::get('accounts/suggest-code', [AccountController::class, 'suggestCode'])->name('accounts.suggest-code');
    Route::get('accounts/parents', [AccountController::class, 'parents'])->name('accounts.parents');
    Route::post('accounts/{account}/toggle-status', [AccountController::class, 'toggleStatus'])->name('accounts.toggle-status');
    Route::post('accounts/{account}/toggle-deprecated', [AccountController::class, 'toggleDeprecated'])->name('accounts.toggle-deprecated');
    Route::resource('accounts', AccountController::class);
});
