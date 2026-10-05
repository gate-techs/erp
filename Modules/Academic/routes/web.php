<?php

use Illuminate\Support\Facades\Route;
use Modules\Academic\Http\Controllers\AcademicClassController;
use Modules\Academic\Http\Controllers\AcademicStageController;
use Modules\Academic\Http\Controllers\AcademicYearController;

Route::middleware(['auth', 'verified'])
    ->prefix('academic')
    ->name('academic.')
    ->group(function () {
        Route::get('academic-years/data', [AcademicYearController::class, 'data'])
            ->name('academic-years.data');
        Route::post('academic-years', [AcademicYearController::class, 'store'])
            ->name('academic-years.store');
        Route::get('academic-years/{id}', [AcademicYearController::class, 'show'])
            ->name('academic-years.show');
        Route::put('academic-years/{id}', [AcademicYearController::class, 'update'])
            ->name('academic-years.update');
        Route::delete('academic-years/{id}', [AcademicYearController::class, 'destroy'])
            ->name('academic-years.destroy');
        Route::patch('academic-years/{id}/toggle-status', [AcademicYearController::class, 'toggleStatus'])
            ->name('academic-years.toggle-status');
        Route::patch('academic-years/{id}/set-current', [AcademicYearController::class, 'setCurrent'])
            ->name('academic-years.set-current');

        Route::get('stages/data', [AcademicStageController::class, 'data'])
            ->name('stages.data');
        Route::post('stages', [AcademicStageController::class, 'store'])
            ->name('stages.store');
        Route::get('stages/{id}', [AcademicStageController::class, 'show'])
            ->name('stages.show');
        Route::put('stages/{id}', [AcademicStageController::class, 'update'])
            ->name('stages.update');
        Route::delete('stages/{id}', [AcademicStageController::class, 'destroy'])
            ->name('stages.destroy');
        Route::patch('stages/{id}/toggle-status', [AcademicStageController::class, 'toggleStatus'])
            ->name('stages.toggle-status');

        Route::get('classes/data', [AcademicClassController::class, 'data'])
            ->name('classes.data');
        Route::post('classes', [AcademicClassController::class, 'store'])
            ->name('classes.store');
        Route::get('classes/{id}', [AcademicClassController::class, 'show'])
            ->name('classes.show');
        Route::put('classes/{id}', [AcademicClassController::class, 'update'])
            ->name('classes.update');
        Route::delete('classes/{id}', [AcademicClassController::class, 'destroy'])
            ->name('classes.destroy');
        Route::patch('classes/{id}/toggle-status', [AcademicClassController::class, 'toggleStatus'])
            ->name('classes.toggle-status');
    });
