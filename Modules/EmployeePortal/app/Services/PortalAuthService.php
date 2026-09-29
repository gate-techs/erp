<?php

namespace Modules\EmployeePortal\Services;

use Illuminate\Support\Facades\Auth;
use Modules\EmployeePortal\Services\Contracts\PortalAuthServiceInterface;

class PortalAuthService implements PortalAuthServiceInterface
{
    /**
     * Attempt to log in with email and password credentials.
     *
     * @param array $credentials
     * @param bool $remember
     * @return array
     */
    public function attemptLogin(array $credentials, bool $remember = false): array
    {
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (isset($user->is_active) && !$user->is_active) {
                Auth::logout();
                return [
                    'success' => false,
                    'message' => __('تم تعطيل هذا الحساب. يرجى التواصل مع إدارة الموارد البشرية.'),
                    'user'    => null,
                ];
            }

            request()->session()->regenerate();

            return [
                'success' => true,
                'message' => __('تم تسجيل الدخول بنجاح. مرحباً بك!'),
                'user'    => $user,
            ];
        }

        return [
            'success' => false,
            'message' => __('البريد الإلكتروني أو كلمة المرور غير صحيحة.'),
            'user'    => null,
        ];
    }

    /**
     * Invalidate current session and log out.
     *
     * @return void
     */
    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}
