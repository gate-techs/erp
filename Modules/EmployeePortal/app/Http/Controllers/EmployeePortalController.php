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
use Modules\EmployeePortal\Services\Contracts\PortalDocumentServiceInterface;
use Modules\EmployeePortal\Services\Contracts\PortalLeaveServiceInterface;
use Modules\EmployeePortal\Services\Contracts\PortalLoanServiceInterface;
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
        protected PortalPermissionServiceInterface $permissionService,
        protected PortalDocumentServiceInterface $documentService,
        protected PortalLoanServiceInterface $loanService
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

        return view('employeeportal::dashboard', $data);
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
        $context = $this->profileService->getProfileContext(Auth::user());

        return view('employeeportal::profile', $context);
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
     * Display Salary Details & Allowances screen (تفاصيل الراتب والبدلات ومسيرات الرواتب).
     */
    public function salaryDetails(): View
    {
        $context = $this->profileService->getSalaryContext(Auth::user());

        return view('employeeportal::salary', $context);
    }

    /**
     * Display Rewards & Honorary Incentives Log screen (سجل المكافآت والحوافز التقديرية).
     */
    public function rewardsLog(Request $request): View
    {
        $filter = $request->query('filter');
        $context = $this->profileService->getRewardsContext(Auth::user(), $filter);

        return view('employeeportal::rewards', $context);
    }

    /**
     * Display Nursery Policies & Regulations screen (سياسات ولوائح الحضانة).
     */
    public function nurseryPolicies(): View
    {
        $context = $this->profileService->getPoliciesContext(Auth::user());

        return view('employeeportal::policies', $context);
    }

    /**
     * Display Notification & Alert Settings screen (إعدادات الإشعارات والتنبيهات).
     */
    public function notificationSettings(): View
    {
        $context = $this->profileService->getNotificationSettingsContext(Auth::user());

        return view('employeeportal::notifications-settings', $context);
    }

    /**
     * Update Notification & Alert Settings.
     */
    public function updateNotificationSettings(Request $request): JsonResponse
    {
        $result = $this->profileService->updateNotificationSettings(Auth::user(), $request->all());

        return response()->json($result);
    }

    /**
     * Fast toggle a single notification preference dynamically via AJAX.
     */
    public function toggleNotificationPreference(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key'   => ['required', 'string', 'max:60'],
            'value' => ['required'],
        ]);

        $boolVal = filter_var($validated['value'], FILTER_VALIDATE_BOOLEAN);

        $result = $this->profileService->toggleSingleNotificationPreference(
            Auth::user(),
            $validated['key'],
            $boolVal
        );

        return response()->json($result);
    }

    /**
     * Display Security & Face ID password screen (الأمان وبصمة الوجه).
     */
    public function securitySettings(): View
    {
        $context = $this->profileService->getSecurityContext(Auth::user());

        return view('employeeportal::security-settings', $context);
    }

    /**
     * Update user password after biometric verification.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ]);

        $result = $this->profileService->updatePassword(Auth::user(), $validated);

        return response()->json($result);
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
     * Display Permission Requests History screen (سجل الأذونات).
     */
    public function permissionsHistory(Request $request): View
    {
        $status = $request->query('status');
        $context = $this->permissionService->getPermissionsHistoryContext(Auth::user(), $status);

        return view('employeeportal::permissions.index', [
            'employee'      => $context['employee'],
            'user'          => $context['user'],
            'balance'       => $context['balance'],
            'statusCounts'  => $context['statusCounts'],
            'permissions'   => $context['permissions'],
            'currentFilter' => $context['currentFilter'],
        ]);
    }

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

    /**
     * Display Permission Request Details screen (تفاصيل الاستئذان).
     */
    public function showPermission(int $id): View|RedirectResponse
    {
        $context = $this->permissionService->getPermissionDetails(Auth::user(), $id);

        if (!$context) {
            return redirect()->route('portal.permissions.index')->with('error', __('Permission request not found or unauthorized.'));
        }

        return view('employeeportal::permissions.show', $context);
    }

    /**
     * Cancel a pending permission request.
     */
    public function cancelPermission(int $id): RedirectResponse
    {
        $result = $this->permissionService->cancelPermissionRequest(Auth::user(), $id);

        if ($result['success']) {
            return redirect()->route('portal.permissions.show', $id)->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    //==========================================================================
    // 5. Official Documents & Files (المستندات والوثائق)
    //==========================================================================

    /**
     * Display employee official documents screen.
     */
    public function documents(): View
    {
        $context = $this->documentService->getDocumentsContext(Auth::user());

        return view('employeeportal::documents.index', $context);
    }

    /**
     * Show form to submit or update official document.
     */
    public function createDocument(Request $request): View
    {
        $documentId = $request->has('id') ? (int) $request->get('id') : null;
        $context = $this->documentService->getCreateDocumentContext(Auth::user(), $documentId);

        return view('employeeportal::documents.create', $context);
    }

    /**
     * Upload or update an employee document.
     */
    public function uploadDocument(Request $request): RedirectResponse
    {
        $request->validate([
            'document_id'        => 'nullable|integer',
            'document_type_id'   => 'required|integer',
            'issuing_authority'  => 'required|string|max:255',
            'document_number'    => 'nullable|string|max:100',
            'expiry_date'        => 'nullable|date',
            'is_permanent'       => 'nullable',
            'file'               => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ]);

        $result = $this->documentService->uploadDocument($request->all(), Auth::user());

        if ($result['success']) {
            return redirect()->route('portal.documents.index')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message'])->withInput();
    }

    //==========================================================================
    // 6. Loans & Salary Advances (السلف والقروض المالية)
    //==========================================================================

    /**
     * Display Loans & Advances History screen (سجل السلف والقروض).
     */
    public function loansHistory(Request $request): View
    {
        $status = $request->query('status');
        $context = $this->loanService->getLoansHistoryContext(Auth::user(), $status);

        return view('employeeportal::loans.index', [
            'employee'           => $context['employee'],
            'user'               => $context['user'],
            'loans'              => $context['loans'],
            'currentFilter'      => $context['currentFilter'],
            'statusCounts'       => $context['statusCounts'],
            'totalRemainingDebt' => $context['totalRemainingDebt'],
            'totalDisbursed'     => $context['totalDisbursed'],
            'totalRepaid'        => $context['totalRepaid'],
            'nextInstallment'    => $context['nextInstallment'],
        ]);
    }

    /**
     * Render Submit New Loan / Salary Advance Request Screen (طلب سلفة مالية جديدة).
     */
    public function createLoan(): View
    {
        $context = $this->loanService->getLoanFormContext(Auth::user());

        return view('employeeportal::loans.create', [
            'employee'    => $context['employee'],
            'user'        => $context['user'],
            'eligibility' => $context['eligibility'],
            'recentLoans' => $context['recentLoans'],
            'loanTypes'   => $context['loanTypes'],
        ]);
    }

    /**
     * AJAX Real-time eligibility, installment & schedule calculation.
     */
    public function calculateLoan(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'amount'             => 'required|numeric|min:0',
            'type'               => 'required|string|in:salary_advance,installment_loan',
            'installments_count' => 'nullable|integer|min:1|max:60',
            'start_date'         => 'nullable|date',
        ]);

        $result = $this->loanService->calculateLoanEligibility(Auth::user(), $validated);

        return response()->json($result);
    }

    /**
     * Handle Loan / Salary Advance Request Submission.
     */
    public function storeLoan(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type'               => 'required|string|in:salary_advance,installment_loan',
            'amount'             => 'required|numeric|min:1',
            'installments_count' => 'nullable|integer|min:1|max:60',
            'start_date'         => 'required|date',
            'reason'             => 'nullable|string|max:1000',
            'attachment'         => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $result = $this->loanService->submitLoanRequest($validated, Auth::user(), $request->file('attachment'));

        if ($result['success']) {
            return redirect()->route('portal.loans.index')->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message'])->withInput();
    }

    /**
     * Display Single Loan Request Details screen with installment breakdown.
     */
    public function showLoan(int $id): View|RedirectResponse
    {
        $context = $this->loanService->getLoanDetails(Auth::user(), $id);

        if (!$context) {
            return redirect()->route('portal.loans.index')->with('error', __('Loan request not found or unauthorized.'));
        }

        return view('employeeportal::loans.show', $context);
    }

    //==========================================================================
    // 7. PWA Infrastructure (Manifest, Service Worker, Offline Fallback)
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

    /**
     * Public Privacy Policy for Apple App Store compliance.
     */
    public function privacy(): View
    {
        return view('employeeportal::privacy');
    }

    /**
     * Public Terms of Use & Conditions.
     */
    public function terms(): View
    {
        return view('employeeportal::terms');
    }

    /**
     * Request Account Deletion & Data Purge (Apple Guideline 5.1.1(v)).
     */
    public function requestAccountDeletion(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
            'reason'   => ['nullable', 'string', 'max:500'],
        ]);

        $user = Auth::user();
        if (!\Illuminate\Support\Facades\Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'success' => false,
                'message' => __('Current password is incorrect.'),
            ], 422);
        }

        // Log the account deletion request
        \Illuminate\Support\Facades\Log::warning("Account deletion requested by user {$user->id} ({$user->email}). Reason: " . ($request->input('reason') ?: 'N/A'));

        // Terminate session for privacy & security
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success'      => true,
            'message'      => __('Your account deletion request has been submitted successfully to HR Administration. Your session has been ended.'),
            'redirect_url' => route('portal.login'),
        ]);
    }
}
