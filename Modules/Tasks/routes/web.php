<?php

use Illuminate\Support\Facades\Route;
use Modules\Tasks\Http\Controllers\TaskController;
use Modules\Tasks\Http\Controllers\TaskGroupController;
use Modules\Tasks\Http\Controllers\TaskMonitoringController;

Route::middleware(['auth', 'verified'])->prefix('tasks')->name('tasks.')->group(function () {
    // 0. Task Monitoring / Dashboard Routes
    Route::get('monitoring', [TaskMonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('monitoring/data', [TaskMonitoringController::class, 'data'])->name('monitoring.data');
    // 1. Task Groups Routes
    Route::get('task-groups/data', [TaskGroupController::class, 'data'])->name('task-groups.data');
    Route::post('task-groups/{task_group}/toggle-status', [TaskGroupController::class, 'toggleStatus'])->name('task-groups.toggle-status');
    Route::resource('task-groups', TaskGroupController::class);

    // 2. Tasks Routes
    Route::get('tasks/data', [TaskController::class, 'data'])->name('tasks.data');
    Route::post('tasks/{task}/update-status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::post('tasks/{task}/notes', [TaskController::class, 'addNote'])->name('tasks.notes.store');
    Route::delete('tasks/attachments/{attachment}', [TaskController::class, 'deleteAttachment'])->name('tasks.attachments.destroy');
    Route::resource('tasks', TaskController::class);
});
