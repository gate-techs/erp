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
use Modules\EmployeePortal\Services\Contracts\PortalLeaveServiceInterface;
use Modules\EmployeePortal\Services\Contracts\PortalPermissionServiceInterface;
use Modules\EmployeePortal\Services\Contracts\PortalProfileServiceInterface;
use Modules\EmployeePortal\Services\Contracts\PortalPwaServiceInterface;

class EmployeePortalController extends Controller
{
    /**
     * Dependency injection of domain services following Interface-Service pattern.
     */
    public function __construct(
        protected PortalAuthServiceInterface $authService,
        protected PortalAttendanceServiceInterface $attendanceService,
        protected PortalPwaServiceInterface $pwaService,
        protected PortalProfileServiceInterface $profileService,
        protected PortalLeaveServiceInterface $leaveService,
        protected PortalPermissionServiceInterface $permissionService
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
            ->with('success', __('تم تسجيل الخروج بنجاح.'));
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

    /**
     * Render Edit Profile Screen (تعديل المعلومات الشخصية).
     */
    public function editProfile(): View
    {
        $data = $this->profileService->getProfileEditData(Auth::user());

        return view('employeeportal::edit-profile', [
            'employee' => $data['employee'],
            'user'     => $data['user'],
            'branches' => $data['branches'] ?? [],
        ]);
    }

    /**
     * Update Profile Data (حفظ التغييرات).
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name_ar'                   => 'nullable|string|max:190',
            'name_en'                   => 'nullable|string|max:190',
            'date_of_birth'             => 'nullable|date',
            'nationality'               => 'nullable|string|max:100',
            'marital_status'            => 'nullable|string|max:50',
            'primary_branch_id'         => 'nullable|exists:nursery_branches,id',
            'phone'                     => 'nullable|string|max:30',
            'alternate_phone'           => 'nullable|string|max:30',
            'email'                     => 'nullable|email|max:190',
            'emergency_contact_name'    => 'nullable|string|max:190',
            'emergency_contact_relation'=> 'nullable|string|max:100',
            'emergency_contact_phone'   => 'nullable|string|max:30',
            'avatar'                    => 'nullable|image|max:4096',
            'password'                  => 'nullable|string|min:6|confirmed',
        ]);

        $result = $this->profileService->updateProfile($validated, Auth::user());

        if ($result['success']) {
            return redirect()->route('portal.profile')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message'])->withInput();
    }

    /**
     * Render Employee Requests Screen (صفحة الطلبات).
     */
    public function requests(): View
    {
        $employee = $this->attendanceService->resolveCurrentEmployee(Auth::user());

        return view('employeeportal::requests', [
            'employee' => $employee,
            'user'     => Auth::user(),
        ]);
    }

    /**
     * Render Submit New Leave Request Screen (طلب إجازة جديد).
     */
    public function createLeave(): View
    {
        $context = $this->leaveService->getLeaveFormContext(Auth::user());

        return view('employeeportal::leaves.create', [
            'employee'      => $context['employee'],
            'user'          => $context['user'],
            'annualBalance' => $context['annualBalance'],
            'leaveTypes'    => $context['leaveTypes'],
        ]);
    }

    /**
     * AJAX Real-time working days and balance calculation.
     */
    public function calculateLeave(Request $request): JsonResponse
    {
        $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $result = $this->leaveService->calculateDaysAndBalance(
            Auth::user(),
            $request->leave_type,
            $request->start_date,
            $request->end_date
        );

        return response()->json($result);
    }

    /**
     * Handle Leave Request Submission.
     */
    public function storeLeave(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'leave_type'              => 'required|string',
            'start_date'              => 'required|date',
            'end_date'                => 'required|date|after_or_equal:start_date',
            'emergency_contact_phone' => 'nullable|string|max:30',
            'reason'                  => 'nullable|string|max:1000',
            'attachment'              => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $result = $this->leaveService->submitLeaveRequest($validated, Auth::user());

        if ($result['success']) {
            return redirect()->route('portal.requests')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message'])->withInput();
    }

    //==========================================================================
    // 4. Permission Requests (تقديم طلب استئذان ومغادرة)
    //==========================================================================

    /**
     * Render Submit New Permission Request Screen (طلب استئذان جديد).
     */
    public function createPermission(): View
    {
        $context = $this->permissionService->getPermissionFormContext(Auth::user());

        return view('employeeportal::permissions.create', [
            'employee'          => $context['employee'],
            'user'              => $context['user'],
            'balance'           => $context['balance'],
            'permissionTypes'   => $context['permissionTypes'],
            'recentPermissions' => $context['recentPermissions'],
        ]);
    }

    /**
     * AJAX Real-time duration & balance calculation for permission request.
     */
    public function calculatePermission(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date'         => 'required|date',
            'type'         => 'required|string',
            'from_time'    => 'required|date_format:H:i',
            'to_time'      => 'required|date_format:H:i|after:from_time',
            'is_emergency' => 'nullable|boolean',
        ]);

        $result = $this->permissionService->calculateDurationAndBalance(Auth::user(), $validated);

        return response()->json($result);
    }

    /**
     * Handle Permission Request Submission.
     */
    public function storePermission(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date'         => 'required|date',
            'type'         => 'required|string|in:late_arrival,early_departure,during_shift,emergency',
            'from_time'    => 'required|date_format:H:i',
            'to_time'      => 'required|date_format:H:i|after:from_time',
            'is_emergency' => 'nullable|boolean',
            'reason'       => 'nullable|string|max:1000',
            'attachment'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $result = $this->permissionService->submitPermissionRequest($validated, Auth::user());

        if ($result['success']) {
            return redirect()->route('portal.requests')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message'])->withInput();
    }

    //==========================================================================
    // 5. PWA Infrastructure (Manifest, Service Worker, Offline Fallback)
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
