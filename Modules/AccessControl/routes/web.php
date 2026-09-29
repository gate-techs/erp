<?php

use Illuminate\Support\Facades\Route;
use Modules\AccessControl\Http\Controllers\RoleController;
use Modules\AccessControl\Http\Controllers\UserController;

Route::middleware(['auth'])->prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/data', [UserController::class, 'data'])->name('data');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{id}', [UserController::class, 'show'])->name('show');
    Route::put('/{id}', [UserController::class, 'update'])->name('update');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    Route::patch('/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
});

Route::middleware(['auth'])->prefix('settings/roles')->name('settings.roles.')->group(function () {
    Route::get('/', [RoleController::class, 'index'])->name('index');
    Route::get('/data', [RoleController::class, 'data'])->name('data');
    Route::post('/', [RoleController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('edit');
    Route::put('/{id}', [RoleController::class, 'update'])->name('update');
    Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy');
});

use Modules\AccessControl\Http\Controllers\ProfileController;

Route::middleware(['auth'])->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::get('/print', [ProfileController::class, 'print'])->name('print');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('update-password');
});
