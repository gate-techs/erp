<?php

namespace Modules\EmployeePortal\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Modules\EmployeePortal\Repositories\Contracts\PortalEmployeeRepositoryInterface;
use Modules\EmployeePortal\Services\Contracts\PortalProfileServiceInterface;
use Modules\EmployeePortal\Models\EmployeeNotificationPreference;
use Modules\HR\Enums\RewardStatus;
use Modules\HR\Models\Employee;
use Modules\HR\Models\HrWorkPolicy;
use Modules\HR\Models\Reward;

class PortalProfileService implements PortalProfileServiceInterface
{
    public function __construct(
        protected PortalEmployeeRepositoryInterface $employeeRepo
    ) {}

    /**
     * Resolve employee model from user or fallback.
     */
    public function resolveCurrentEmployee(?User $user): ?Employee
    {
        if ($user) {
            $employee = $this->employeeRepo->findByUserId($user->id);
            if ($employee) {
                return $employee;
            }
        }

        return $this->employeeRepo->getActiveFallback();
    }

    /**
     * Get data required for employee profile screen.
     */
    public function getProfileContext(?User $user): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        $name = $employee?->name ?? $user?->name ?? __('Staff Member');
        $jobTitle = $employee?->jobTitle?->name ?? $employee?->designation?->name ?? __('Kindergarten Educator');
        $department = $employee?->department?->name ?? '';
        $roleSummary = $department ? ($jobTitle . ' • ' . $department) : $jobTitle;

        $branchName = $employee?->primaryBranch?->name ?? $employee?->branch?->name ?? __('Main Branch - State of Kuwait');

        $hireDateFormatted = $employee?->hire_date
            ? $employee->hire_date->translatedFormat('d F Y')
            : \Carbon\Carbon::now()->subYears(2)->translatedFormat('d F Y');

        $shiftName = $employee?->shift?->name ?? __('Full-time Morning Shift');
        $jobSubtitle = __('Hire Date: :date • :shift', [
            'date'  => $hireDateFormatted,
            'shift' => $shiftName,
        ]);

        $latestPayrollMonth = \Carbon\Carbon::now()->subMonth()->translatedFormat('F');

        $avatarUrl = $employee?->avatar_url ?? $user?->avatar_url ?? asset('assets/portal/teacher-avatar.png');

        $currentLocale = app()->getLocale();
        $targetLocale = $currentLocale === 'ar' ? 'en' : 'ar';
        $targetLocaleLabel = $currentLocale === 'ar' ? 'English' : __('Arabic');
        $currentLocaleLabel = $currentLocale === 'ar' ? __('Arabic') : 'English';
        $langSwitchUrl = route('lang.swap', $targetLocale);
        $langSubtitle = $currentLocale === 'ar'
            ? __('Arabic (Active) • Switch to English')
            : __('English (Active) • Switch to Arabic');

        return [
            'employee'              => $employee,
            'user'                  => $user,
            'name'                  => $name,
            'role_summary'          => $roleSummary,
            'branch_name'           => $branchName,
            'job_subtitle'          => $jobSubtitle,
            'latest_payroll_month'  => $latestPayrollMonth,
            'avatar_url'            => $avatarUrl,
            'current_locale'        => $currentLocale,
            'target_locale'         => $targetLocale,
            'target_locale_label'   => $targetLocaleLabel,
            'current_locale_label'  => $currentLocaleLabel,
            'lang_switch_url'       => $langSwitchUrl,
            'lang_subtitle'         => $langSubtitle,
        ];
    }

    /**
     * Get profile and edit view data for current employee/user.
     */
    public function getProfileEditData(?User $user): array
    {
        $employee = $this->resolveCurrentEmployee($user);
        $branches = \Modules\Nursery\Models\NurseryBranch::with('translations')->get();

        return [
            'employee' => $employee,
            'user'     => $user,
            'branches' => $branches,
        ];
    }

    /**
     * Update employee personal and contact details.
     */
    public function updateProfile(array $data, ?User $user): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        if (!$employee) {
            return [
                'success' => false,
                'message' => __('Employee record not found.'),
            ];
        }

        try {
            DB::beginTransaction();

            $updatePayload = [];

            // Handle Avatar upload if provided
            if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile) {
                $file = $data['avatar'];
                $path = $file->store('employees/avatars', 'public');
                $updatePayload['avatar'] = $path;

                // Sync with user avatar if exists
                if ($user) {
                    $user->update(['avatar' => $path]);
                }

                // Mirror file to public storage folder for direct web serving
                try {
                    $publicDir = public_path('storage/employees/avatars');
                    if (!file_exists($publicDir)) {
                        @mkdir($publicDir, 0755, true);
                    }
                    @copy(storage_path('app/public/' . $path), public_path('storage/' . $path));
                } catch (\Throwable $e) {
                    // Suppress mirror copy failure
                }
            }

            // Translatable and Core attributes
            if (!empty($data['name_ar'])) {
                $updatePayload['ar'] = array_merge($updatePayload['ar'] ?? [], [
                    'name' => $data['name_ar'],
                ]);
            }

            if (!empty($data['name_en'])) {
                $updatePayload['en'] = array_merge($updatePayload['en'] ?? [], [
                    'name' => $data['name_en'],
                ]);
            }

            // Personal & Branch attributes
            if (isset($data['date_of_birth'])) {
                $updatePayload['date_of_birth'] = $data['date_of_birth'];
            }
            if (isset($data['nationality'])) {
                $updatePayload['nationality'] = $data['nationality'];
            }
            if (isset($data['marital_status'])) {
                $updatePayload['marital_status'] = $data['marital_status'];
            }
            if (isset($data['primary_branch_id'])) {
                $updatePayload['primary_branch_id'] = $data['primary_branch_id'];
            }

            // Contact attributes
            if (isset($data['phone'])) {
                $updatePayload['phone'] = $data['phone'];
            }
            if (isset($data['alternate_phone'])) {
                $updatePayload['alternate_phone'] = $data['alternate_phone'];
            }
            if (isset($data['email'])) {
                $updatePayload['email'] = $data['email'];
                if ($user && $user->email !== $data['email']) {
                    $user->update(['email' => $data['email']]);
                }
            }
            if (isset($data['emergency_contact_name'])) {
                $updatePayload['emergency_contact_name'] = $data['emergency_contact_name'];
            }
            if (isset($data['emergency_contact_relation'])) {
                $updatePayload['emergency_contact_relation'] = $data['emergency_contact_relation'];
            }
            if (isset($data['emergency_contact_phone'])) {
                $updatePayload['emergency_contact_phone'] = $data['emergency_contact_phone'];
            }

            // Password update
            if (!empty($data['password']) && $user) {
                $user->update([
                    'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
                ]);
            }

            // Update Employee via Repository
            $updatedEmployee = $this->employeeRepo->update($employee, $updatePayload);

            DB::commit();

            return [
                'success'  => true,
                'message'  => __('تم حفظ وتحديث البيانات بنجاح.'),
                'employee' => $updatedEmployee,
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PortalProfileService update error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => __('حدث خطأ أثناء حفظ البيانات: ') . $e->getMessage(),
            ];
        }
    }

    /**
     * Get data required for employee salary & allowances screen.
     */
    public function getSalaryContext(?User $user): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        if ($employee) {
            $employee->load([
                'bank',
                'bankBranch',
                'allowances.allowanceType',
                'payrollItems' => function ($q) {
                    $q->with('payroll')->latest('id')->take(12);
                },
                'loans' => function ($q) {
                    $q->whereIn('status', ['approved', 'disbursed'])->with('installments');
                }
            ]);

            $basicSalary       = (float) ($employee->basic_salary ?? 0);
            $allowances        = $employee->allowances ?? collect();
            $totalAllowances   = (float) $allowances->sum('amount');
            $grossSalary       = $basicSalary + $totalAllowances;
            $insurancePct      = (float) ($employee->social_insurance_percentage ?? 0);
            $insuranceAmount   = round($basicSalary * ($insurancePct / 100), 2);
            $activeLoansDebt   = (float) ($employee->loans ? $employee->loans->sum('remaining_amount') : 0);
            $monthlyLoansCut   = (float) ($employee->loans ? $employee->loans->sum('monthly_installment') : 0);
            $netEstimated      = max(0, $grossSalary - $insuranceAmount - $monthlyLoansCut);

            $payrollHistory    = $employee->payrollItems ?? collect();
            $latestPayroll     = $payrollHistory->first();

            $bankInfo = [
                'bank_name'      => $employee->bank?->name ?? $employee->bank_name ?? '—',
                'account_name'   => $employee->bank_account_name ?? $employee->name ?? '—',
                'account_number' => $employee->bank_account_number ?? '—',
                'iban'           => $employee->iban ?? '—',
            ];
        } else {
            $basicSalary     = 0.0;
            $allowances      = collect();
            $totalAllowances = 0.0;
            $grossSalary     = 0.0;
            $insurancePct    = 0.0;
            $insuranceAmount = 0.0;
            $activeLoansDebt = 0.0;
            $monthlyLoansCut = 0.0;
            $netEstimated    = 0.0;
            $payrollHistory  = collect();
            $latestPayroll   = null;
            $bankInfo        = [
                'bank_name'      => '—',
                'account_name'   => '—',
                'account_number' => '—',
                'iban'           => '—',
            ];
        }

        return [
            'employee'         => $employee,
            'user'             => $user,
            'basicSalary'      => $basicSalary,
            'allowances'       => $allowances,
            'totalAllowances'  => $totalAllowances,
            'grossSalary'      => $grossSalary,
            'insurancePct'     => $insurancePct,
            'insuranceAmount'  => $insuranceAmount,
            'activeLoansDebt'  => $activeLoansDebt,
            'monthlyLoansCut'  => $monthlyLoansCut,
            'netEstimated'     => $netEstimated,
            'payrollHistory'   => $payrollHistory,
            'latestPayroll'    => $latestPayroll,
            'bankInfo'         => $bankInfo,
        ];
    }

    /**
     * Get data required for employee awards & incentives log screen.
     */
    public function getRewardsContext(?User $user, ?string $filter = null): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        if ($employee) {
            $baseQuery = Reward::where('employee_id', $employee->id)->with('rewardType');

            $allRewards = (clone $baseQuery)->latest('award_date')->latest('id')->get();

            // Counts & Totals
            $totalCount = $allRewards->count();
            $totalFinancialAmount = $allRewards->whereIn('status', [RewardStatus::APPROVED, RewardStatus::PAID])->sum('amount');
            $honoraryCount = $allRewards->filter(function ($r) {
                return (float)$r->amount <= 0;
            })->count();

            $financialCount = $totalCount - $honoraryCount;

            // Apply filter
            $filteredRewards = $allRewards;
            if ($filter === 'financial') {
                $filteredRewards = $allRewards->filter(fn($r) => (float)$r->amount > 0);
            } elseif ($filter === 'honorary') {
                $filteredRewards = $allRewards->filter(fn($r) => (float)$r->amount <= 0);
            }

            $filteredRewards->transform(function ($reward) {
                $reward->is_financial = (float)$reward->amount > 0;
                $reward->type_title = $reward->rewardType?->name ?? __('Excellence & Appreciation Award');
                $reward->formatted_date = $reward->award_date 
                    ? $reward->award_date->translatedFormat('d F Y')
                    : ($reward->created_at ? $reward->created_at->translatedFormat('d F Y') : '—');
                return $reward;
            });
        } else {
            $filteredRewards = collect();
            $totalCount = 0;
            $totalFinancialAmount = 0.0;
            $honoraryCount = 0;
            $financialCount = 0;
        }

        return [
            'employee'             => $employee,
            'user'                 => $user,
            'rewards'              => $filteredRewards,
            'totalCount'           => $totalCount,
            'totalFinancialAmount' => (float) $totalFinancialAmount,
            'honoraryCount'        => $honoraryCount,
            'financialCount'       => $financialCount,
            'currentFilter'        => $filter ?: 'all',
        ];
    }

    /**
     * Get data required for nursery policies & regulations screen.
     */
    public function getPoliciesContext(?User $user): array
    {
        $employee = $this->resolveCurrentEmployee($user);
        if ($employee) {
            $employee->loadMissing(['shift', 'branch', 'department', 'jobTitle']);
        }

        /** @var \Modules\HR\Repositories\Contracts\WorkPolicyRepositoryInterface $policyRepo */
        $policyRepo = app(\Modules\HR\Repositories\Contracts\WorkPolicyRepositoryInterface::class);
        $grouped = $policyRepo->getGroupedPolicies();

        $leavesPolicies      = $grouped['leaves'] ?? [];
        $absencePolicies     = $grouped['absence'] ?? [];
        $noticeEosPolicies   = $grouped['notice_and_eos'] ?? [];
        $permissionsPolicies = $grouped['permissions'] ?? [];
        $loansPolicies       = $grouped['loans'] ?? [];
        $sickLeaveTiers      = $policyRepo->getSickLeaveTiers();
        $eosRules            = $policyRepo->getEosRules();

        $shift  = $employee?->shift;
        $branch = $employee?->branch;

        $annualDays       = (int)($leavesPolicies['annual_leave_days'] ?? 21);
        $eligMonths       = (int)($leavesPolicies['first_year_eligibility_months'] ?? 3);
        $rolloverDays     = (int)($leavesPolicies['max_rollover_days'] ?? 15);
        $allowRollover    = !empty($leavesPolicies['allow_annual_rollover']);
        $casualDays       = (int)($leavesPolicies['casual_leave_annual_days'] ?? 6);
        $maternityDays    = (int)($leavesPolicies['maternity_leave_days'] ?? 70);
        $marriageDays     = (int)($leavesPolicies['marriage_leave_days'] ?? 5);
        $bereaveDays      = (int)($leavesPolicies['bereavement_leave_days'] ?? 5);
        $paternityDays    = (int)($leavesPolicies['paternity_leave_days'] ?? 3);
        $hajjDays         = (int)($leavesPolicies['hajj_leave_days'] ?? 10);
        $hajjYears        = (int)($leavesPolicies['hajj_min_service_years'] ?? 2);

        $permMonthlyHours = (int)($permissionsPolicies['max_monthly_permission_hours'] ?? 4);
        $permMonthlyCount = (int)($permissionsPolicies['max_monthly_permission_count'] ?? 3);
        $permSingleHours  = (int)($permissionsPolicies['max_single_permission_hours'] ?? 2);
        $permNoticeHours  = (int)($permissionsPolicies['min_advance_notice_hours'] ?? 24);
        $permEmergency    = (int)($permissionsPolicies['max_monthly_emergency_permissions'] ?? 1);
        $graceMins        = (int)($permissionsPolicies['morning_grace_period_minutes'] ?? ($shift?->grace_period_in ?? 15));

        $loanMinMonths    = (int)($loansPolicies['min_service_months'] ?? 3);
        $loanMaxMulti     = (float)($loansPolicies['max_salary_multiplier'] ?? 3.0);
        $advanceMaxMulti  = (float)($loansPolicies['max_advance_multiplier'] ?? 1.0);
        $loanMaxDeduct    = (float)($loansPolicies['max_monthly_deduction_percentage'] ?? 33.3);
        $loanMaxMonths    = (int)($loansPolicies['max_installments_months'] ?? 24);
        $allowMultiLoans  = !empty($loansPolicies['allow_multiple_loans']);

        $noticeMonthly    = (int)($noticeEosPolicies['notice_period_monthly_months'] ?? 3);
        $eosFirst5        = (int)($noticeEosPolicies['eos_days_monthly_first_5_years'] ?? 15);
        $eosAfter5        = (int)($noticeEosPolicies['eos_days_monthly_after_5_years'] ?? 30);

        $shiftTitle       = $shift ? ($shift->name ?? __('Standard Morning Shift')) : __('Standard Morning Shift');
        $isStartAm        = ($shift && $shift->start_time) ? (\Carbon\Carbon::parse($shift->start_time)->format('A') === 'AM') : true;
        $shiftStart       = ($shift && $shift->start_time) ? \Carbon\Carbon::parse($shift->start_time)->format('h:i') . ' ' . ($isStartAm ? __('AM') : __('PM')) : '07:30 ' . __('AM');
        $isEndAm          = ($shift && $shift->end_time) ? (\Carbon\Carbon::parse($shift->end_time)->format('A') === 'AM') : false;
        $shiftEnd         = ($shift && $shift->end_time) ? \Carbon\Carbon::parse($shift->end_time)->format('h:i') . ' ' . ($isEndAm ? __('AM') : __('PM')) : '02:30 ' . __('PM');
        $shiftHours       = $shift ? (float)$shift->total_hours : 7.0;
        $branchName       = $branch ? ($branch->name ?? __('Astro Leaders Nursery Campus')) : __('Astro Leaders Nursery Campus');
        $maxAbsenceDays   = (int)($absencePolicies['max_monthly_deduction_days'] ?? 5);
        $continuousDays   = (int)($absencePolicies['continuous_absence_termination_days'] ?? 15);
        $intermittentDays = (int)($absencePolicies['intermittent_absence_termination_days'] ?? 30);

        $sickLeaveTiers->transform(function ($tier) {
            $tier->display_name = app()->getLocale() === 'ar' ? ($tier->name_ar ?? $tier->name_en) : ($tier->name_en ?? $tier->name_ar);
            return $tier;
        });

        $eosRules->transform(function ($rule) {
            $rule->display_name = app()->getLocale() === 'ar' ? ($rule->name_ar ?? $rule->name_en) : ($rule->name_en ?? $rule->name_ar);
            return $rule;
        });

        return [
            'employee'            => $employee,
            'user'                => $user,
            'shift'               => $shift,
            'branch'              => $branch,
            'leavesPolicies'      => $leavesPolicies,
            'absencePolicies'     => $absencePolicies,
            'noticeEosPolicies'   => $noticeEosPolicies,
            'permissionsPolicies' => $permissionsPolicies,
            'loansPolicies'       => $loansPolicies,
            'sickLeaveTiers'      => $sickLeaveTiers,
            'eosRules'            => $eosRules,
            // Pre-calculated & formatted attributes for clean Blade views
            'annualDays'          => $annualDays,
            'eligMonths'          => $eligMonths,
            'rolloverDays'        => $rolloverDays,
            'allowRollover'       => $allowRollover,
            'casualDays'          => $casualDays,
            'maternityDays'       => $maternityDays,
            'marriageDays'        => $marriageDays,
            'bereaveDays'         => $bereaveDays,
            'paternityDays'       => $paternityDays,
            'hajjDays'            => $hajjDays,
            'hajjYears'           => $hajjYears,
            'permMonthlyHours'    => $permMonthlyHours,
            'permMonthlyCount'    => $permMonthlyCount,
            'permSingleHours'     => $permSingleHours,
            'permNoticeHours'     => $permNoticeHours,
            'permEmergency'       => $permEmergency,
            'graceMins'           => $graceMins,
            'loanMinMonths'       => $loanMinMonths,
            'loanMaxMulti'        => $loanMaxMulti,
            'advanceMaxMulti'     => $advanceMaxMulti,
            'loanMaxDeduct'       => $loanMaxDeduct,
            'loanMaxMonths'       => $loanMaxMonths,
            'allowMultiLoans'     => $allowMultiLoans,
            'noticeMonthly'       => $noticeMonthly,
            'eosFirst5'           => $eosFirst5,
            'eosAfter5'           => $eosAfter5,
            'shiftTitle'          => $shiftTitle,
            'shiftStart'          => $shiftStart,
            'shiftEnd'            => $shiftEnd,
            'shiftHours'          => $shiftHours,
            'branchName'          => $branchName,
            'maxAbsenceDays'      => $maxAbsenceDays,
            'continuousDays'      => $continuousDays,
            'intermittentDays'    => $intermittentDays,
        ];
    }

    /**
     * Get data required for employee notification settings screen.
     */
    public function getNotificationSettingsContext(?User $user): array
    {
        $employee = $this->resolveCurrentEmployee($user);
        if ($employee) {
            $employee->loadMissing(['shift', 'branch']);
        }

        $userId = $user?->id ?? $employee?->user_id;

        $preferences = EmployeeNotificationPreference::firstOrCreate(
            ['user_id' => $userId],
            [
                'employee_id'               => $employee?->id,
                'morning_reminder_enabled'  => true,
                'morning_reminder_mins'     => 15,
                'checkout_reminder_enabled' => true,
                'punch_success_alert'       => true,
                'payroll_alert_enabled'     => true,
                'loans_alert_enabled'       => true,
                'rewards_alert_enabled'     => true,
                'leave_requests_alert'      => true,
                'permission_requests_alert' => true,
                'admin_memos_alert'         => true,
                'policy_updates_alert'      => true,
                'push_enabled'              => true,
                'whatsapp_enabled'          => false,
                'email_enabled'             => true,
                'quiet_hours_enabled'       => false,
                'quiet_hours_start'         => '20:00:00',
                'quiet_hours_end'           => '07:00:00',
            ]
        );

        $hasActivePushSubscription = $userId
            ? DB::table('push_subscriptions')->where('user_id', $userId)->exists()
            : false;

        $shift = $employee?->shift;
        $shiftName = $shift ? ($shift->name ?? __('Standard Morning Shift')) : __('Standard Morning Shift');
        $isStartAm = ($shift && $shift->start_time) ? (\Carbon\Carbon::parse($shift->start_time)->format('A') === 'AM') : true;
        $shiftStart = ($shift && $shift->start_time) ? \Carbon\Carbon::parse($shift->start_time)->format('h:i') . ' ' . ($isStartAm ? __('AM') : __('PM')) : '07:30 ' . __('AM');

        return [
            'employee'                  => $employee,
            'user'                      => $user,
            'preferences'               => $preferences,
            'hasActivePushSubscription' => $hasActivePushSubscription,
            'shiftName'                 => $shiftName,
            'shiftStart'                => $shiftStart,
            'vapidPublicKey'            => config('webpush.vapid.public_key', ''),
        ];
    }

    /**
     * Update employee notification settings.
     */
    public function updateNotificationSettings(?User $user, array $data): array
    {
        $employee = $this->resolveCurrentEmployee($user);
        $userId = $user?->id ?? $employee?->user_id;

        if (!$userId) {
            return [
                'success' => false,
                'message' => __('Employee record not found.'),
            ];
        }

        $preferences = EmployeeNotificationPreference::firstOrCreate(
            ['user_id' => $userId],
            ['employee_id' => $employee?->id]
        );

        $payload = [];
        $booleanKeys = [
            'morning_reminder_enabled',
            'checkout_reminder_enabled',
            'punch_success_alert',
            'payroll_alert_enabled',
            'loans_alert_enabled',
            'rewards_alert_enabled',
            'leave_requests_alert',
            'permission_requests_alert',
            'admin_memos_alert',
            'policy_updates_alert',
            'push_enabled',
            'whatsapp_enabled',
            'email_enabled',
            'quiet_hours_enabled',
        ];

        foreach ($booleanKeys as $key) {
            if (array_key_exists($key, $data)) {
                $payload[$key] = filter_var($data[$key], FILTER_VALIDATE_BOOLEAN);
            }
        }

        if (isset($data['morning_reminder_mins'])) {
            $mins = (int) $data['morning_reminder_mins'];
            $payload['morning_reminder_mins'] = in_array($mins, [5, 10, 15, 30, 45, 60]) ? $mins : 15;
        }

        if (!empty($data['quiet_hours_start'])) {
            $payload['quiet_hours_start'] = $data['quiet_hours_start'];
        }

        if (!empty($data['quiet_hours_end'])) {
            $payload['quiet_hours_end'] = $data['quiet_hours_end'];
        }

        $preferences->update($payload);

        return [
            'success'     => true,
            'preferences' => $preferences->fresh(),
            'message'     => __('Notification settings saved successfully.'),
        ];
    }

    /**
     * Toggle a single notification preference dynamically via AJAX.
     */
    public function toggleSingleNotificationPreference(?User $user, string $key, bool $value): array
    {
        $employee = $this->resolveCurrentEmployee($user);
        $userId = $user?->id ?? $employee?->user_id;

        if (!$userId) {
            return [
                'success' => false,
                'message' => __('Employee record not found.'),
            ];
        }

        $allowedKeys = [
            'morning_reminder_enabled',
            'checkout_reminder_enabled',
            'punch_success_alert',
            'payroll_alert_enabled',
            'loans_alert_enabled',
            'rewards_alert_enabled',
            'leave_requests_alert',
            'permission_requests_alert',
            'admin_memos_alert',
            'policy_updates_alert',
            'push_enabled',
            'whatsapp_enabled',
            'email_enabled',
            'quiet_hours_enabled',
        ];

        if (!in_array($key, $allowedKeys, true)) {
            return [
                'success' => false,
                'message' => __('Invalid notification setting key.'),
            ];
        }

        $preferences = EmployeeNotificationPreference::firstOrCreate(
            ['user_id' => $userId],
            ['employee_id' => $employee?->id]
        );

        $preferences->update([$key => $value]);

        return [
            'success' => true,
            'key'     => $key,
            'value'   => $value,
            'message' => __('Setting updated successfully.'),
        ];
    }

    /**
     * Get data required for employee security & password settings screen.
     */
    public function getSecurityContext(?User $user): array
    {
        $employee = $this->resolveCurrentEmployee($user);

        return [
            'user'     => $user,
            'employee' => $employee,
            'userName' => $employee?->name ?? $user?->name ?? __('Staff Member'),
            'userEmail'=> $user?->email ?? $employee?->email ?? '',
        ];
    }

    /**
     * Update user password with biometric verification.
     */
    public function updatePassword(?User $user, array $data): array
    {
        if (!$user) {
            return [
                'success' => false,
                'message' => __('User not found or unauthenticated.'),
            ];
        }

        $newPassword = $data['password'] ?? '';
        $passwordConfirmation = $data['password_confirmation'] ?? '';

        if (strlen($newPassword) < 8) {
            return [
                'success' => false,
                'message' => __('Password must be at least 8 characters.'),
            ];
        }

        if ($newPassword !== $passwordConfirmation) {
            return [
                'success' => false,
                'message' => __('Passwords do not match.'),
            ];
        }

        $user->password = bcrypt($newPassword);
        $user->save();

        return [
            'success' => true,
            'message' => __('Password updated successfully.'),
        ];
    }
}

