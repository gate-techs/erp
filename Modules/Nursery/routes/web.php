<?php

use Illuminate\Support\Facades\Route;
use Modules\Nursery\Http\Controllers\NurseryBranchController;
use Modules\Nursery\Http\Controllers\NurseryController;

Route::middleware(['auth'])->group(function () {
    // Nurseries Routes
    Route::get('nurseries/data', [NurseryController::class, 'data'])->name('nursery.data');
    Route::patch('nurseries/{id}/toggle-status', [NurseryController::class, 'toggleStatus'])->name('nursery.toggle-status');
    Route::resource('nurseries', NurseryController::class)->names('nursery');

    // Nursery Branches Routes
    Route::get('nursery-branches/data', [NurseryBranchController::class, 'data'])->name('nursery.branches.data');
    Route::patch('nursery-branches/{id}/toggle-status', [NurseryBranchController::class, 'toggleStatus'])->name('nursery.branches.toggle-status');
    Route::get('nursery-branches/cities/{governmentId}', [NurseryBranchController::class, 'getCities'])->name('nursery.branches.cities');
    Route::resource('nursery-branches', NurseryBranchController::class)->names('nursery.branches');
});
