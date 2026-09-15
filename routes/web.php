<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\dashboard\Analytics;

// Language Switcher Route
Route::get('lang/{locale}', [LanguageController::class, 'swap'])->name('lang.swap');

// Authentication Routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// Protected Application Routes (Authenticated)
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Main Dashboard Route
    Route::get('/', [Analytics::class, 'index'])->name('dashboard-analytics');
});