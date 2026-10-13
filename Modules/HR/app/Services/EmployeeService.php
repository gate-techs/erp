<?php

namespace Modules\HR\Services;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\HR\Enums\EmployeeStatus;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\Gender;
use Modules\HR\Enums\MaritalStatus;
use Modules\HR\Models\Department;
use Modules\HR\Models\Employee;
use Modules\HR\Models\HrSetting;
use Modules\HR\Models\JobTitle;
use Modules\HR\Models\Shift;
use Modules\HR\Models\WorkSchedule;
use Modules\HR\Repositories\Contracts\EmployeeRepositoryInterface;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Settings\Enums\GeneralSettingType;
use Modules\Settings\Models\Bank;
use Modules\Settings\Models\GeneralSetting;
use Modules\AccessControl\Models\Role;

class EmployeeService
{
    //======================
    public function __construct(
        protected EmployeeRepositoryInterface $employeeRepository
    ) {}

    //======================
    /**
     * Get query builder instance for server-side DataTables.
     */
    public function getDataTableQuery(array $filters = []): Builder
    {
        return $this->employeeRepository->getDataTableQuery($filters);
    }

    //======================
    /**
     * Get lookup form data needed for create/edit employee screens.
     *
     * @return array<string, mixed>
     */
    public function getFormData(): array
    {
        return [
            'genders'         => class_exists(GeneralSetting::class) && GeneralSetting::ofType(GeneralSettingType::GENDER)->exists() ? GeneralSetting::ofType(GeneralSettingType::GENDER)->with('translations')->get() : Gender::cases(),
            'maritalStatuses' => class_exists(GeneralSetting::class) ? GeneralSetting::ofType(GeneralSettingType::MARITAL_STATUS)->with('translations')->get() : collect(),
            'nationalities'   => class_exists(GeneralSetting::class) ? GeneralSetting::ofType(GeneralSettingType::NATIONALITY)->with('translations')->get() : collect(),
            'religions'       => class_exists(GeneralSetting::class) ? GeneralSetting::ofType(GeneralSettingType::RELIGION)->with('translations')->get() : collect(),
            'bloodTypes'      => class_exists(GeneralSetting::class) ? GeneralSetting::ofType(GeneralSettingType::BLOOD_TYPE)->with('translations')->get() : collect(),
            'relationships'   => class_exists(GeneralSetting::class) ? GeneralSetting::ofType(GeneralSettingType::RELATIONSHIP)->with('translations')->get() : collect(),
            'governorates'    => class_exists(GeneralSetting::class) ? GeneralSetting::ofType(GeneralSettingType::GOVERNMENT)->with('translations')->get() : collect(),
            'cities'          => class_exists(GeneralSetting::class) ? GeneralSetting::ofType(GeneralSettingType::CITY)->with('translations')->get() : collect(),
            'employmentTypes' => EmploymentType::cases(),
            'statuses'        => EmployeeStatus::cases(),
            'branches'        => class_exists(NurseryBranch::class) ? NurseryBranch::with('translations')->get() : collect(),
            'administrations' => Department::with('translations')->whereNull('parent_id')->where('status', 'active')->get(),
            'departments'     => Department::with('translations')->whereNotNull('parent_id')->where('status', 'active')->get(),
            'allDepartments'  => Department::with('translations')->where('status', 'active')->get(),
            'jobTitles'       => JobTitle::with('translations')->where('status', 'active')->get(),
            'managers'        => Employee::with('translations')->where('status', EmployeeStatus::ACTIVE)->get(),
            'workSchedules'   => WorkSchedule::with('translations')->where('status', 'active')->get(),
            'shifts'          => Shift::with('translations')->where('status', 'active')->get(),
            'banks'           => Bank::with('translations')->whereNull('parent_id')->get(),
            'bankBranches'    => Bank::with('translations')->whereNotNull('parent_id')->get(),
            'visaTypes'       => HrSetting::with('translations')->where('type', 'visa-types')->get(),
            'allowanceTypes'  => HrSetting::with('translations')->where('type', 'allowance-types')->get(),
            'documentTypes'   => HrSetting::with('translations')->where('type', 'document-types')->get(),
            'roles'           => class_exists(Role::class) ? Role::all() : collect(),
            'nextCode'        => $this->employeeRepository->getNextEmployeeCode(),
        ];
    }

    //======================
    /**
     * Find employee by ID.
     */
    public function getEmployeeById(int $id): ?Employee
    {
        return $this->employeeRepository->findById($id);
    }

    //======================
    /**
     * Get full employee profile data for the profile view.
     *
     * @return array{employee: Employee, kpis: array}|null
     */
    public function getProfileData(int $id): ?array
    {
        $employee = $this->employeeRepository->findWithFullProfile($id);

        if (!$employee) {
            return null;
        }

        // ---- KPI: Basic Salary ----
        $basicSalary = (float) ($employee->basic_salary ?? 0);

        // ---- KPI: Active Allowances Total ----
        $allowancesTotal = $employee->allowances
            ->where('is_active', true)
            ->sum('amount');

        // ---- KPI: Service Duration ----
        $serviceYears  = null;
        $serviceMonths = null;
        if ($employee->hire_date) {
            $serviceYears  = (int) now()->diffInYears($employee->hire_date);
            $serviceMonths = (int) now()->subYears($serviceYears)->diffInMonths($employee->hire_date);
        }

        // ---- KPI: Attendance this month ----
        $attendanceDays = $employee->attendances()
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();

        // ---- Stats: Totals ----
        $activeLoan      = $employee->loans->where('status', 'disbursed')->first();
        $totalRewardsYTD = $employee->rewards
            ->where('status', 'paid')
            ->filter(fn($r) => $r->created_at && $r->created_at->year === now()->year)
            ->sum('amount');

        return [
            'employee' => $employee,
            'kpis'     => [
                'basic_salary'      => $basicSalary,
                'allowances_total'  => $allowancesTotal,
                'service_years'     => $serviceYears,
                'service_months'    => $serviceMonths,
                'attendance_days'   => $attendanceDays,
                'active_loan'       => $activeLoan,
                'total_rewards_ytd' => $totalRewardsYTD,
            ],
        ];
    }

    //======================
    /**
     * Create a new employee record.
     */
    public function createEmployee(array $validated, ?int $authUserId = null): Employee
    {
        return DB::transaction(function () use ($validated, $authUserId) {
            // 1. Handle Avatar Upload
            $avatarPath = null;
            if (isset($validated['avatar']) && $validated['avatar'] instanceof UploadedFile) {
                $avatarPath = $validated['avatar']->store('employees/avatars', 'public');
            }

            // 2. Handle User Account Creation if requested
            $userId = !empty($validated['user_id']) ? (int) $validated['user_id'] : null;
            $isTeacher = !empty($validated['is_teacher']);
            if (!empty($validated['create_user_account']) && !empty($validated['email']) && !$userId) {
                $userType = $isTeacher ? UserType::TEACHER : UserType::EMPLOYEE;
                $rawPassword = !empty($validated['user_password']) ? $validated['user_password'] : 'astro@123';
                $user = User::create([
                    'name'      => $validated['name_ar'],
                    'email'     => $validated['email'],
                    'phone'     => $validated['phone'] ?? null,
                    'password'  => Hash::make($rawPassword),
                    'user_type' => $userType,
                    'is_active' => true,
                ]);
                $roleName = !empty($validated['role']) ? $validated['role'] : ($isTeacher ? 'teacher' : 'employee');
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole($roleName);
                }
                $userId = $user->id;
            }

            // 3. Build Core Payload
            $payload = array_merge(
                $this->formatTranslationData($validated),
                [
                    'employee_code'               => !empty($validated['employee_code']) ? $validated['employee_code'] : null,
                    'is_teacher'                  => $isTeacher,
                    'user_id'                     => $userId,
                    'avatar'                      => $avatarPath,
                    'gender'                      => $validated['gender'] ?? Gender::MALE->value,
                    'marital_status'              => $validated['marital_status'] ?? null,
                    'religion'                    => $validated['religion'] ?? null,
                    'nationality'                 => $validated['nationality'] ?? null,
                    'date_of_birth'               => $validated['date_of_birth'] ?? null,
                    'national_id'                 => $validated['national_id'] ?? null,
                    'blood_type'                  => $validated['blood_type'] ?? null,
                    'visa_type_id'                => !empty($validated['visa_type_id']) ? (int) $validated['visa_type_id'] : null,
                    'visa_expiry_date'            => $validated['visa_expiry_date'] ?? null,
                    'passport_number'             => $validated['passport_number'] ?? null,
                    'passport_issue_date'         => $validated['passport_issue_date'] ?? null,
                    'passport_expiry_date'        => $validated['passport_expiry_date'] ?? null,
                    'primary_branch_id'           => !empty($validated['primary_branch_id']) ? (int) $validated['primary_branch_id'] : null,
                    'department_id'               => !empty($validated['department_id']) ? (int) $validated['department_id'] : null,
                    'job_title_id'                => !empty($validated['job_title_id']) ? (int) $validated['job_title_id'] : null,
                    'manager_id'                  => !empty($validated['manager_id']) ? (int) $validated['manager_id'] : null,
                    'work_schedule_id'            => !empty($validated['work_schedule_id']) ? (int) $validated['work_schedule_id'] : null,
                    'shift_id'                    => !empty($validated['shift_id']) ? (int) $validated['shift_id'] : null,
                    'employment_type'             => $validated['employment_type'] ?? EmploymentType::FULL_TIME->value,
                    'hire_date'                   => $validated['hire_date'] ?? now()->toDateString(),
                    'probation_end_date'          => $validated['probation_end_date'] ?? null,
                    'contract_end_date'           => $validated['contract_end_date'] ?? null,
                    'status'                      => $validated['status'] ?? EmployeeStatus::ACTIVE->value,
                    'phone'                       => $validated['phone'],
                    'alternate_phone'             => $validated['alternate_phone'] ?? null,
                    'email'                       => $validated['email'] ?? null,
                    'governorate_id'              => !empty($validated['governorate_id']) ? (int) $validated['governorate_id'] : null,
                    'city'                        => $validated['city'] ?? null,
                    'emergency_contact_name'      => $validated['emergency_contact_name'] ?? null,
                    'emergency_contact_relation'  => $validated['emergency_contact_relation'] ?? null,
                    'emergency_contact_phone'     => $validated['emergency_contact_phone'] ?? null,
                    'bank_id'                     => !empty($validated['bank_id']) ? (int) $validated['bank_id'] : null,
                    'bank_branch_id'              => !empty($validated['bank_branch_id']) ? (int) $validated['bank_branch_id'] : null,
                    'account_holder_name'         => $validated['account_holder_name'] ?? null,
                    'account_number'              => $validated['account_number'] ?? null,
                    'iban'                        => $validated['iban'] ?? null,
                    'basic_salary'                => (float) ($validated['basic_salary'] ?? 0),
                    'hourly_rate'                 => !empty($validated['hourly_rate']) ? (float) $validated['hourly_rate'] : null,
                    'daily_working_hours'         => (float) ($validated['daily_working_hours'] ?? 8.0),
                    'social_insurance_number'     => $validated['social_insurance_number'] ?? null,
                    'social_insurance_percentage' => (float) ($validated['social_insurance_percentage'] ?? 0),
                    'created_by'                  => $authUserId,
                ]
            );

            $branches = $validated['branches'] ?? [];
            if (!empty($validated['primary_branch_id']) && !in_array($validated['primary_branch_id'], $branches)) {
                $branches[] = $validated['primary_branch_id'];
            }

            $allowances = $validated['allowances'] ?? [];
            $documents = $this->processUploadedDocuments($validated['documents'] ?? []);

            return $this->employeeRepository->create($payload, $branches, $allowances, $documents);
        });
    }

    //======================
    /**
     * Update an existing employee record.
     */
    public function updateEmployee(Employee $employee, array $validated): Employee
    {
        return DB::transaction(function () use ($employee, $validated) {
            // 1. Handle Avatar Update
            $avatarPath = $employee->avatar;
            if (isset($validated['avatar']) && $validated['avatar'] instanceof UploadedFile) {
                if ($employee->avatar && Storage::disk('public')->exists($employee->avatar)) {
                    Storage::disk('public')->delete($employee->avatar);
                }
                $avatarPath = $validated['avatar']->store('employees/avatars', 'public');
            }

            // 2. Handle User Account Linking / Password update / Role update
            $userId = $employee->user_id;
            if (!empty($validated['user_id'])) {
                $userId = (int) $validated['user_id'];
                $user = User::find($userId);
                if ($user && !empty($validated['role']) && method_exists($user, 'syncRoles')) {
                    $user->syncRoles([$validated['role']]);
                }
            } elseif (!empty($validated['create_user_account']) && !$userId && !empty($validated['email'])) {
                $user = User::create([
                    'name'      => $validated['name_ar'],
                    'email'     => $validated['email'],
                    'phone'     => $validated['phone'] ?? null,
                    'password'  => Hash::make($validated['user_password'] ?? '12345678'),
                    'user_type' => UserType::EMPLOYEE,
                    'is_active' => true,
                ]);
                $roleName = !empty($validated['role']) ? $validated['role'] : 'employee';
                if (method_exists($user, 'assignRole')) {
                    $user->assignRole($roleName);
                }
                $userId = $user->id;
            } elseif ($userId) {
                $user = User::find($userId);
                if ($user) {
                    $userUpdates = [];
                    if (!empty($validated['name_ar'])) {
                        $userUpdates['name'] = $validated['name_ar'];
                    }
                    if (!empty($validated['email'])) {
                        $userUpdates['email'] = $validated['email'];
                    }
                    if (!empty($validated['user_password'])) {
                        $userUpdates['password'] = Hash::make($validated['user_password']);
                    }
                    if (!empty($userUpdates)) {
                        $user->update($userUpdates);
                    }
                    if (!empty($validated['role']) && method_exists($user, 'syncRoles')) {
                        $user->syncRoles([$validated['role']]);
                    }
                }
            }

            // 3. Build Core Payload
            $payload = array_merge(
                $this->formatTranslationData($validated),
                [
                    'employee_code'               => !empty($validated['employee_code']) ? $validated['employee_code'] : $employee->employee_code,
                    'is_teacher'                  => isset($validated['is_teacher']) ? (bool) $validated['is_teacher'] : (bool) $employee->is_teacher,
                    'user_id'                     => $userId,
                    'avatar'                      => $avatarPath,
                    'gender'                      => $validated['gender'] ?? ($employee->gender instanceof \BackedEnum ? $employee->gender->value : $employee->gender),
                    'marital_status'              => $validated['marital_status'] ?? $employee->marital_status,
                    'religion'                    => $validated['religion'] ?? $employee->religion,
                    'nationality'                 => $validated['nationality'] ?? $employee->nationality,
                    'date_of_birth'               => $validated['date_of_birth'] ?? $employee->date_of_birth?->toDateString(),
                    'national_id'                 => $validated['national_id'] ?? $employee->national_id,
                    'blood_type'                  => $validated['blood_type'] ?? $employee->blood_type,
                    'visa_type_id'                => !empty($validated['visa_type_id']) ? (int) $validated['visa_type_id'] : null,
                    'visa_expiry_date'            => $validated['visa_expiry_date'] ?? null,
                    'passport_number'             => $validated['passport_number'] ?? null,
                    'passport_issue_date'         => $validated['passport_issue_date'] ?? null,
                    'passport_expiry_date'        => $validated['passport_expiry_date'] ?? null,
                    'primary_branch_id'           => !empty($validated['primary_branch_id']) ? (int) $validated['primary_branch_id'] : null,
                    'department_id'               => !empty($validated['department_id']) ? (int) $validated['department_id'] : null,
                    'job_title_id'                => !empty($validated['job_title_id']) ? (int) $validated['job_title_id'] : null,
                    'manager_id'                  => !empty($validated['manager_id']) ? (int) $validated['manager_id'] : null,
                    'work_schedule_id'            => !empty($validated['work_schedule_id']) ? (int) $validated['work_schedule_id'] : null,
                    'shift_id'                    => !empty($validated['shift_id']) ? (int) $validated['shift_id'] : null,
                    'employment_type'             => $validated['employment_type'] ?? $employee->employment_type->value,
                    'hire_date'                   => $validated['hire_date'] ?? $employee->hire_date?->toDateString(),
                    'probation_end_date'          => $validated['probation_end_date'] ?? null,
                    'contract_end_date'           => $validated['contract_end_date'] ?? null,
                    'status'                      => $validated['status'] ?? $employee->status->value,
                    'phone'                       => $validated['phone'],
                    'alternate_phone'             => $validated['alternate_phone'] ?? null,
                    'email'                       => $validated['email'] ?? null,
                    'governorate_id'              => !empty($validated['governorate_id']) ? (int) $validated['governorate_id'] : null,
                    'city'                        => $validated['city'] ?? null,
                    'emergency_contact_name'      => $validated['emergency_contact_name'] ?? null,
                    'emergency_contact_relation'  => $validated['emergency_contact_relation'] ?? null,
                    'emergency_contact_phone'     => $validated['emergency_contact_phone'] ?? null,
                    'bank_id'                     => !empty($validated['bank_id']) ? (int) $validated['bank_id'] : null,
                    'bank_branch_id'              => !empty($validated['bank_branch_id']) ? (int) $validated['bank_branch_id'] : null,
                    'account_holder_name'         => $validated['account_holder_name'] ?? null,
                    'account_number'              => $validated['account_number'] ?? null,
                    'iban'                        => $validated['iban'] ?? null,
                    'basic_salary'                => (float) ($validated['basic_salary'] ?? 0),
                    'hourly_rate'                 => !empty($validated['hourly_rate']) ? (float) $validated['hourly_rate'] : null,
                    'daily_working_hours'         => (float) ($validated['daily_working_hours'] ?? 8.0),
                    'social_insurance_number'     => $validated['social_insurance_number'] ?? null,
                    'social_insurance_percentage' => (float) ($validated['social_insurance_percentage'] ?? 0),
                ]
            );

            $branches = isset($validated['branches']) ? (array) $validated['branches'] : null;
            if ($branches !== null && !empty($validated['primary_branch_id']) && !in_array($validated['primary_branch_id'], $branches)) {
                $branches[] = $validated['primary_branch_id'];
            }

            $allowances = isset($validated['allowances']) ? (array) $validated['allowances'] : null;
            $documents = isset($validated['documents']) ? $this->processUploadedDocuments($validated['documents']) : null;

            return $this->employeeRepository->update($employee, $payload, $branches, $allowances, $documents);
        });
    }

    //======================
    /**
     * Delete an employee record.
     */
    public function deleteEmployee(Employee $employee): bool
    {
        return $this->employeeRepository->delete($employee);
    }

    //======================
    /**
     * Process documents array and save uploaded files to storage.
     *
     * @param array $rawDocuments
     * @return array
     */
    protected function processUploadedDocuments(array $rawDocuments): array
    {
        $processed = [];

        foreach ($rawDocuments as $doc) {
            $filePath = $doc['file_path'] ?? null;
            $fileName = $doc['file_name'] ?? null;

            if (isset($doc['file']) && $doc['file'] instanceof UploadedFile) {
                $fileName = $doc['file']->getClientOriginalName();
                $filePath = $doc['file']->store('employees/documents', 'public');
            }

            if (!$filePath) {
                continue;
            }

            $processed[] = [
                'document_type_id'   => !empty($doc['document_type_id']) ? (int) $doc['document_type_id'] : null,
                'document_number'    => $doc['document_number'] ?? null,
                'file_path'          => $filePath,
                'file_name'          => $fileName ?? basename($filePath),
                'issue_date'         => $doc['issue_date'] ?? null,
                'expiry_date'        => $doc['expiry_date'] ?? null,
                'notify_before_days' => (int) ($doc['notify_before_days'] ?? 30),
                'notes'              => $doc['notes'] ?? null,
            ];
        }

        return $processed;
    }

    //======================
    /**
     * Format bilingual translation attributes for Astrotomic Translatable.
     *
     * @param array $validated
     * @return array
     */
    protected function formatTranslationData(array $validated): array
    {
        return [
            'ar' => [
                'name'      => $validated['name_ar'],
                'address_1' => $validated['address_1_ar'] ?? null,
                'address_2' => $validated['address_2_ar'] ?? null,
                'notes'     => $validated['notes_ar'] ?? null,
            ],
            'en' => [
                'name'      => !empty($validated['name_en']) ? $validated['name_en'] : $validated['name_ar'],
                'address_1' => $validated['address_1_en'] ?? null,
                'address_2' => $validated['address_2_en'] ?? null,
                'notes'     => $validated['notes_en'] ?? null,
            ],
        ];
    }
}
