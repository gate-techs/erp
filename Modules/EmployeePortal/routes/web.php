<?php

use Illuminate\Support\Facades\Route;
use Modules\EmployeePortal\Http\Controllers\EmployeePortalController;
use Modules\EmployeePortal\Http\Controllers\PortalPushSubscriptionController;

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

        // Profile Screen (الملف الشخصي)
        Route::get('profile', [EmployeePortalController::class, 'profile'])->name('profile');

        // Edit Profile Screen & Update (تعديل المعلومات الشخصية)
        Route::get('profile/edit', [EmployeePortalController::class, 'editProfile'])->name('profile.edit');
        Route::post('profile/update', [EmployeePortalController::class, 'updateProfile'])->name('profile.update');

        // Requests Screen (صفحة الطلبات)
        Route::get('requests', [EmployeePortalController::class, 'requests'])->name('requests');

        // Leave Requests (تقديم طلب إجازة)
        Route::get('leaves/create', [EmployeePortalController::class, 'createLeave'])->name('leaves.create');
        Route::post('leaves/calculate', [EmployeePortalController::class, 'calculateLeave'])->name('leaves.calculate');
        Route::post('leaves/store', [EmployeePortalController::class, 'storeLeave'])->name('leaves.store');

        // Permission Requests (سجل الأذونات وتقديم طلب جديد)
        Route::get('permissions', [EmployeePortalController::class, 'permissionsHistory'])->name('permissions.index');
        Route::get('permissions/create', [EmployeePortalController::class, 'createPermission'])->name('permissions.create');
        Route::post('permissions/calculate', [EmployeePortalController::class, 'calculatePermission'])->name('permissions.calculate');
        Route::post('permissions/store', [EmployeePortalController::class, 'storePermission'])->name('permissions.store');

        Route::post('push-subscriptions', [PortalPushSubscriptionController::class, 'store'])->name('push.subscribe');
        Route::delete('push-subscriptions', [PortalPushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');
    });
});
