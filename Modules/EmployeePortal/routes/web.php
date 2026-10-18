<?php

use Illuminate\Support\Facades\Route;
use Modules\EmployeePortal\Http\Controllers\EmployeePortalController;
use Modules\EmployeePortal\Http\Controllers\PortalNotificationController;
use Modules\EmployeePortal\Http\Controllers\PortalPushSubscriptionController;

Route::prefix('portal')->name('portal.')->group(function () {
    // 0. Entrypoint (Redirects to Splash Screen)
    Route::get('/', function () {
        return redirect()->route('portal.splash');
    });

    // 1. Splash Screen
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
        // Main Screen (Dashboard & Punch)
        Route::get('dashboard', [EmployeePortalController::class, 'dashboard'])->name('dashboard');

        // Attendance & GPS Punch Action
        Route::post('attendance/punch', [EmployeePortalController::class, 'recordPunch'])->name('attendance.punch');

        // Profile Screen
        Route::get('profile', [EmployeePortalController::class, 'profile'])->name('profile');

        // Edit Profile Screen & Update
        Route::get('profile/edit', [EmployeePortalController::class, 'editProfile'])->name('profile.edit');
        Route::post('profile/update', [EmployeePortalController::class, 'updateProfile'])->name('profile.update');

        // Salary Details & Payslips
        Route::get('profile/salary', [EmployeePortalController::class, 'salaryDetails'])->name('profile.salary');

        // Rewards & Honorary Incentives Log
        Route::get('profile/rewards', [EmployeePortalController::class, 'rewardsLog'])->name('profile.rewards');

        // Nursery Policies & Regulations
        Route::get('profile/policies', [EmployeePortalController::class, 'nurseryPolicies'])->name('profile.policies');

        // Notification & Alert Settings
        Route::get('profile/notifications', [EmployeePortalController::class, 'notificationSettings'])->name('profile.notifications');
        Route::post('profile/notifications/update', [EmployeePortalController::class, 'updateNotificationSettings'])->name('profile.notifications.update');
        Route::post('profile/notifications/toggle', [EmployeePortalController::class, 'toggleNotificationPreference'])->name('profile.notifications.toggle');

        // Security & Face ID Password Settings
        Route::get('profile/security', [EmployeePortalController::class, 'securitySettings'])->name('profile.security');
        Route::post('profile/security/password', [EmployeePortalController::class, 'updatePassword'])->name('profile.security.password');

        // Notifications Center
        Route::get('notifications', [PortalNotificationController::class, 'index'])->name('notifications.index');
        Route::post('notifications/{id}/read', [PortalNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('notifications/read-all', [PortalNotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
        Route::delete('notifications/{id}', [PortalNotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::post('notifications/clear-all', [PortalNotificationController::class, 'clearAll'])->name('notifications.clearAll');

        // Requests Screen
        Route::get('requests', [EmployeePortalController::class, 'requests'])->name('requests');

        // Leave Requests
        Route::get('leaves/create', [EmployeePortalController::class, 'createLeave'])->name('leaves.create');
        Route::post('leaves/calculate', [EmployeePortalController::class, 'calculateLeave'])->name('leaves.calculate');
        Route::post('leaves/store', [EmployeePortalController::class, 'storeLeave'])->name('leaves.store');

        // Permission Requests
        Route::get('permissions', [EmployeePortalController::class, 'permissionsHistory'])->name('permissions.index');
        Route::get('permissions/create', [EmployeePortalController::class, 'createPermission'])->name('permissions.create');
        Route::post('permissions/calculate', [EmployeePortalController::class, 'calculatePermission'])->name('permissions.calculate');
        Route::post('permissions/store', [EmployeePortalController::class, 'storePermission'])->name('permissions.store');
        Route::get('permissions/{id}', [EmployeePortalController::class, 'showPermission'])->name('permissions.show');
        Route::post('permissions/{id}/cancel', [EmployeePortalController::class, 'cancelPermission'])->name('permissions.cancel');

        // Loans & Salary Advances
        Route::get('loans', [EmployeePortalController::class, 'loansHistory'])->name('loans.index');
        Route::get('loans/create', [EmployeePortalController::class, 'createLoan'])->name('loans.create');
        Route::post('loans/calculate', [EmployeePortalController::class, 'calculateLoan'])->name('loans.calculate');
        Route::post('loans/store', [EmployeePortalController::class, 'storeLoan'])->name('loans.store');
        Route::get('loans/{id}', [EmployeePortalController::class, 'showLoan'])->name('loans.show');

        // Official Documents Archive
        Route::get('documents', [EmployeePortalController::class, 'documents'])->name('documents.index');
        Route::get('documents/create', [EmployeePortalController::class, 'createDocument'])->name('documents.create');
        Route::post('documents/upload', [EmployeePortalController::class, 'uploadDocument'])->name('documents.upload');

        Route::post('push-subscriptions', [PortalPushSubscriptionController::class, 'store'])->name('push.subscribe');
        Route::delete('push-subscriptions', [PortalPushSubscriptionController::class, 'destroy'])->name('push.unsubscribe');
    });
});
