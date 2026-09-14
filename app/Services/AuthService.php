<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected UserRepositoryInterface $userRepository;

    //==================================
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    //==================================
    /**
     * Authenticate the user and regenerate session.
     *
     * @param string $email
     * @param string $password
     * @param bool $remember
     * @param Request $request
     * @return bool
     * @throws ValidationException
     */
    public function login(string $email, string $password, bool $remember, Request $request): bool
    {
        $credentials = [
            'email' => $email,
            'password' => $password,
        ];

        if (!Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => __('بيانات الاعتماد المدخلة غير متطابقة مع سجلاتنا.'),
            ]);
        }

        $request->session()->regenerate();

        return true;
    }

    //==================================
    /**
     * Log the user out and invalidate the session.
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request): void
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    //==================================
    /**
     * Get the currently authenticated user.
     *
     * @return \App\Models\User|null
     */
    public function getAuthenticatedUser()
    {
        return Auth::user();
    }
}
