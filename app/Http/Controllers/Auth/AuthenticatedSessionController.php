<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    protected AuthService $authService;

    //==================================
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    //==================================
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    //==================================
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        try {
            $this->authService->login(
                $request->validated('email'),
                $request->validated('password'),
                $request->boolean('remember'),
                $request
            );

            $request->clearRateLimit();

            $user = auth()->user();
            $isAdmin = $user && (
                $user->hasAnyRole(['super_admin', 'admin', 'Super Admin', 'Admin']) ||
                $user->user_type === \App\Enums\UserType::ADMIN
            );

            if (! $isAdmin) {
                return redirect()->route('profile.show');
            }

            return redirect()->intended(route('dashboard-analytics'));
        } catch (ValidationException $e) {
            $request->hitRateLimit();
            throw $e;
        }
    }

    //==================================
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->authService->logout($request);

        return redirect()->route('login');
    }
}
