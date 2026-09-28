<?php

namespace Modules\EmployeePortal\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Modules\EmployeePortal\Http\Requests\PortalLoginRequest;
use Modules\EmployeePortal\Http\Requests\PortalPunchRequest;
use Modules\EmployeePortal\Services\Contracts\PortalAttendanceServiceInterface;
use Modules\EmployeePortal\Services\Contracts\PortalAuthServiceInterface;
use Modules\EmployeePortal\Services\Contracts\PortalPwaServiceInterface;

class EmployeePortalController extends Controller
{
    /**
     * Dependency injection of domain services following Interface-Service pattern.
     */
    public function __construct(
        protected PortalAuthServiceInterface $authService,
        protected PortalAttendanceServiceInterface $attendanceService,
        protected PortalPwaServiceInterface $pwaService
    ) {}

    //==========================================================================
    // 1. Splash Screen
    //==========================================================================

    /**
     * Mobile PWA Splash Screen with animated orbit and galaxy gradient.
     */
    public function splash(): View
    {
        return view('employeeportal::splash');
    }

    //==========================================================================
    // 2. Authentication (Login & Logout)
    //==========================================================================

    /**
     * Show Mobile Login Screen.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('portal.dashboard');
        }

        return view('employeeportal::login');
    }

    /**
     * Handle Mobile Login submission via PortalAuthService.
     */
    public function login(PortalLoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        $result = $this->authService->attemptLogin($credentials, $remember);

        if ($result['success']) {
            return redirect()->intended(route('portal.dashboard'));
        }

        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->with('error', $result['message']);
    }

    /**
     * Handle Mobile Logout via PortalAuthService.
     */
    public function logout(): RedirectResponse
    {
        $this->authService->logout();

        return redirect()->route('portal.login')
            ->with('success', 'تم تسجيل الخروج بنجاح.');
    }

    //==========================================================================
    // 3. Post-Login Main Attendance Punch Screen (شاشة البصمة بعد الدخول)
    //==========================================================================

    /**
     * Render Main Attendance Dashboard Screen.
     */
    public function dashboard(): View
    {
        $data = $this->attendanceService->getDashboardData(Auth::user());

        return view('employeeportal::dashboard', [
            'employee'        => $data['employee'],
            'todayAttendance' => $data['todayAttendance'],
        ]);
    }

    /**
     * Record Check-in / Check-out with GPS coordinates via PortalAttendanceService.
     */
    public function recordPunch(PortalPunchRequest $request): JsonResponse|RedirectResponse
    {
        $result = $this->attendanceService->recordPunch($request->validated(), Auth::user());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data'    => $result['data'] ?? null,
            ], $result['status'] ?? ($result['success'] ? 200 : 400));
        }

        if ($result['success']) {
            return redirect()->route('portal.dashboard')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Render Employee Profile Screen (الملف الشخصي) with logout button.
     */
    public function profile(): View
    {
        $employee = $this->attendanceService->resolveCurrentEmployee(Auth::user());

        return view('employeeportal::profile', [
            'employee' => $employee,
            'user'     => Auth::user(),
        ]);
    }

    //==========================================================================
    // 4. PWA Infrastructure (Manifest, Service Worker, Offline Fallback)
    //==========================================================================

    /**
     * Dynamic Web App Manifest via PortalPwaService.
     */
    public function manifest(): JsonResponse
    {
        return response()->json($this->pwaService->getManifest())
            ->header('Content-Type', 'application/manifest+json; charset=utf-8');
    }

    /**
     * Service Worker JavaScript script via PortalPwaService.
     */
    public function serviceWorker(): Response
    {
        return response($this->pwaService->getServiceWorkerScript(), 200, [
            'Content-Type'           => 'application/javascript; charset=utf-8',
            'Service-Worker-Allowed' => '/portal',
            'Cache-Control'          => 'no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * Offline screen.
     */
    public function offline(): View
    {
        return view('employeeportal::offline');
    }
}
