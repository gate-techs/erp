<?php

use Illuminate\Support\Facades\Route;
use Modules\EmployeePortal\Http\Controllers\EmployeePortalController;

Route::prefix('portal')->name('portal.')->group(function () {
    // 0. Entrypoint (Redirects to Splash Every Time App Opens)
    Route::get('/', function () {
        return redirect()->route('portal.splash');
    });

    // 1. Splash Screen (Entry Screen)
    Route::get('splash', [EmployeePortalController::class, 'splash'])->name('splash');

    // 2. Mobile Login & Logout
    Route::get('login', [EmployeePortalController::class, 'showLogin'])->name('login');
    Route::post('login', [EmployeePortalController::class, 'login'])->name('login.submit');
    Route::post('logout', [EmployeePortalController::class, 'logout'])->name('logout');

    // 3. PWA Manifest & Service Worker & Offline Fallback
    Route::get('manifest.json', [EmployeePortalController::class, 'manifest'])->name('manifest');
    Route::get('sw.js', [EmployeePortalController::class, 'serviceWorker'])->name('sw');
    Route::get('offline', [EmployeePortalController::class, 'offline'])->name('offline');

    // 4. Authenticated Mobile Employee Portal Routes
    Route::middleware(['auth'])->group(function () {
        // Main Screen (شاشة البصمة بعد الدخول)
        Route::get('dashboard', [EmployeePortalController::class, 'dashboard'])->name('dashboard');

        // Attendance & GPS Punch Action
        Route::post('attendance/punch', [EmployeePortalController::class, 'recordPunch'])->name('attendance.punch');

        // Profile Screen (الملف الشخصي و زر تسجيل الخروج)
        Route::get('profile', [EmployeePortalController::class, 'profile'])->name('profile');
    });
});

