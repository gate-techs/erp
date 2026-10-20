<?php

use Illuminate\Support\Facades\Route;
use Modules\Nursery\Http\Controllers\NurseryBranchController;
use Modules\Nursery\Http\Controllers\NurseryController;
use Modules\Nursery\Http\Controllers\PackageController;

Route::middleware(['auth'])->group(function () {
    // =============================================================
    // 1. Nurseries Routes (الحضانات)
    // =============================================================
    Route::middleware('permission:nursery.view')->group(function () {
        Route::get('nurseries', [NurseryController::class, 'index'])->name('nursery.index');
        Route::get('nurseries/data', [NurseryController::class, 'data'])->name('nursery.data');
        Route::get('nurseries/{id}', [NurseryController::class, 'show'])->name('nursery.show')->whereNumber('id');
    });

    Route::middleware('permission:nursery.create')->group(function () {
        Route::get('nurseries/create', [NurseryController::class, 'create'])->name('nursery.create');
        Route::post('nurseries', [NurseryController::class, 'store'])->name('nursery.store');
    });

    Route::middleware('permission:nursery.edit')->group(function () {
        Route::get('nurseries/{id}/edit', [NurseryController::class, 'edit'])->name('nursery.edit')->whereNumber('id');
        Route::match(['put', 'patch'], 'nurseries/{id}', [NurseryController::class, 'update'])->name('nursery.update')->whereNumber('id');
    });

    Route::delete('nurseries/{id}', [NurseryController::class, 'destroy'])
        ->middleware('permission:nursery.delete')
        ->name('nursery.destroy')
        ->whereNumber('id');

    Route::patch('nurseries/{id}/toggle-status', [NurseryController::class, 'toggleStatus'])
        ->middleware('permission:nursery.toggle-status')
        ->name('nursery.toggle-status')
        ->whereNumber('id');

    // =============================================================
    // 2. Nursery Branches Routes (فروع الحضانات)
    // =============================================================
    Route::middleware('permission:nursery.branches.view')->group(function () {
        Route::get('nursery-branches', [NurseryBranchController::class, 'index'])->name('nursery.branches.index');
        Route::get('nursery-branches/data', [NurseryBranchController::class, 'data'])->name('nursery.branches.data');
        Route::get('nursery-branches/cities/{governmentId}', [NurseryBranchController::class, 'getCities'])->name('nursery.branches.cities');
        Route::get('nursery-branches/{id}', [NurseryBranchController::class, 'show'])->name('nursery.branches.show')->whereNumber('id');
    });

    Route::middleware('permission:nursery.branches.create')->group(function () {
        Route::get('nursery-branches/create', [NurseryBranchController::class, 'create'])->name('nursery.branches.create');
        Route::post('nursery-branches', [NurseryBranchController::class, 'store'])->name('nursery.branches.store');
    });

    Route::middleware('permission:nursery.branches.edit')->group(function () {
        Route::get('nursery-branches/{id}/edit', [NurseryBranchController::class, 'edit'])->name('nursery.branches.edit')->whereNumber('id');
        Route::match(['put', 'patch'], 'nursery-branches/{id}', [NurseryBranchController::class, 'update'])->name('nursery.branches.update')->whereNumber('id');
    });

    Route::delete('nursery-branches/{id}', [NurseryBranchController::class, 'destroy'])
        ->middleware('permission:nursery.branches.delete')
        ->name('nursery.branches.destroy')
        ->whereNumber('id');

    Route::patch('nursery-branches/{id}/toggle-status', [NurseryBranchController::class, 'toggleStatus'])
        ->middleware('permission:nursery.branches.toggle-status')
        ->name('nursery.branches.toggle-status')
        ->whereNumber('id');

    // =============================================================
    // 3. Packages Routes / الباقات
    // =============================================================
    Route::middleware('permission:nursery.packages.view')->group(function () {
        Route::get('packages', [PackageController::class, 'index'])->name('packages.index');
        Route::get('packages/data', [PackageController::class, 'data'])->name('packages.data');
        Route::get('packages/{package}', [PackageController::class, 'show'])->name('packages.show');
    });
    Route::middleware('permission:nursery.packages.create')->group(function () {
        Route::get('packages/create', [PackageController::class, 'create'])->name('packages.create');
        Route::post('packages', [PackageController::class, 'store'])->name('packages.store');
    });
    Route::middleware('permission:nursery.packages.edit')->group(function () {
        Route::get('packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit');
        Route::match(['put', 'patch'], 'packages/{package}', [PackageController::class, 'update'])->name('packages.update');
    });
    Route::delete('packages/{package}', [PackageController::class, 'destroy'])
        ->middleware('permission:nursery.packages.delete')
        ->name('packages.destroy');
    Route::patch('packages/{id}/toggle-status', [PackageController::class, 'toggleStatus'])
        ->middleware('permission:nursery.packages.toggle-status')
        ->name('packages.toggle-status');
});
