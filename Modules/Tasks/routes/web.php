<?php

use Illuminate\Support\Facades\Route;
use Modules\Tasks\Http\Controllers\TaskController;
use Modules\Tasks\Http\Controllers\TaskGroupController;
use Modules\Tasks\Http\Controllers\TaskMonitoringController;

Route::middleware(['auth', 'verified'])->prefix('tasks')->name('tasks.')->group(function () {
    // 0. Task Monitoring / Dashboard Routes
    Route::middleware('permission:tasks.monitoring.view')->group(function () {
        Route::get('monitoring', [TaskMonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('monitoring/data', [TaskMonitoringController::class, 'data'])->name('monitoring.data');
    });

    // 1. Task Groups Routes
    Route::middleware('permission:tasks.task-groups.view')->group(function () {
        Route::get('task-groups/data', [TaskGroupController::class, 'data'])->name('task-groups.data');
        Route::get('task-groups', [TaskGroupController::class, 'index'])->name('task-groups.index');
        Route::get('task-groups/{task_group}', [TaskGroupController::class, 'show'])->name('task-groups.show');
        Route::get('task-groups/{task_group}/edit', [TaskGroupController::class, 'edit'])->name('task-groups.edit');
    });
    Route::post('task-groups', [TaskGroupController::class, 'store'])
        ->middleware('permission:tasks.task-groups.create')
        ->name('task-groups.store');
    Route::match(['put', 'patch'], 'task-groups/{task_group}', [TaskGroupController::class, 'update'])
        ->middleware('permission:tasks.task-groups.edit')
        ->name('task-groups.update');
    Route::delete('task-groups/{task_group}', [TaskGroupController::class, 'destroy'])
        ->middleware('permission:tasks.task-groups.delete')
        ->name('task-groups.destroy');
    Route::post('task-groups/{task_group}/toggle-status', [TaskGroupController::class, 'toggleStatus'])
        ->middleware('permission:tasks.task-groups.toggle-status')
        ->name('task-groups.toggle-status');

    // 2. Tasks Routes
    Route::middleware('permission:tasks.view')->group(function () {
        Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index');
        Route::get('tasks/data', [TaskController::class, 'data'])->name('tasks.data');
        Route::get('tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
        Route::get('tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    });
    Route::post('tasks', [TaskController::class, 'store'])
        ->middleware('permission:tasks.create')
        ->name('tasks.store');
    Route::match(['put', 'patch'], 'tasks/{task}', [TaskController::class, 'update'])
        ->middleware('permission:tasks.edit')
        ->name('tasks.update');
    Route::delete('tasks/{task}', [TaskController::class, 'destroy'])
        ->middleware('permission:tasks.delete')
        ->name('tasks.destroy');

    Route::post('tasks/{task}/update-status', [TaskController::class, 'updateStatus'])
        ->middleware('permission:tasks.toggle-status')
        ->name('tasks.update-status');
    Route::post('tasks/{task}/notes', [TaskController::class, 'addNote'])
        ->middleware('permission:tasks.toggle-status')
        ->name('tasks.notes.store');
    Route::delete('tasks/attachments/{attachment}', [TaskController::class, 'deleteAttachment'])
        ->middleware('permission:tasks.edit')
        ->name('tasks.attachments.destroy');
});
