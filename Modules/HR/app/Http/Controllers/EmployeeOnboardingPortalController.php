<?php

namespace Modules\HR\Http\Controllers;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeDocument;
use Modules\HR\Services\EmployeeService;
use Spatie\Permission\Models\Role;

class EmployeeOnboardingPortalController extends Controller
{
    public function __construct(
        protected EmployeeService $employeeService
    ) {}

    /**
     * Display the public self-registration form for any brand new employee.
     */
    public function createPublic(): View
    {
        if (!session()->has('locale')) {
            session()->put('locale', 'en');
            app()->setLocale('en');
        }

        $employee = new Employee();
        $formData = $this->employeeService->getFormData();

        return view('hr::onboarding_portal.form', array_merge($formData, [
            'employee' => $employee,
            'isPublic' => true,
        ]));
    }

    /**
     * Store brand new employee record from public onboarding registration.
     */
    public function storePublic(Request $request): View
    {
        $validated = $request->validate([
            'name_ar'                  => ['required', 'string', 'max:255'],
            'name_en'                  => ['nullable', 'string', 'max:255'],
            'primary_branch_id'        => ['required', 'integer', 'exists:nursery_branches,id'],
            'gender'                   => ['required', 'string', 'in:male,female'],
            'marital_status'           => ['nullable', 'string', 'max:50'],
            'date_of_birth'            => ['nullable', 'date'],
            'national_id'              => ['required', 'string', 'max:50', 'unique:employees,national_id'],
            'nationality'              => ['nullable', 'string', 'max:50'],
            'blood_type'               => ['nullable', 'string', 'max:5'],
            'phone'                    => ['required', 'string', 'max:30'],
            'alternate_phone'          => ['nullable', 'string', 'max:30'],
            'email'                    => ['nullable', 'string', 'max:100'],
            'governorate_id'           => ['nullable', 'integer'],
            'city'                     => ['nullable', 'string', 'max:100'],
            'address_1_ar'             => ['nullable', 'string', 'max:255'],
            'emergency_contact_name'   => ['nullable', 'string', 'max:100'],
            'emergency_contact_relation'=> ['nullable', 'string', 'max:50'],
            'emergency_contact_phone'  => ['nullable', 'string', 'max:30'],
            'bank_id'                  => ['nullable', 'integer'],
            'account_holder_name'      => ['nullable', 'string', 'max:150'],
            'account_number'           => ['nullable', 'string', 'max:50'],
            'iban'                     => ['nullable', 'string', 'max:50'],
            'avatar'                   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'documents'                => ['nullable', 'array'],
            'documents.*.file'         => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg,webp', 'max:5120'],
            'doc_national_id'          => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
            'doc_certificate'          => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
            'doc_other'                => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
        ]);

        $employee = DB::transaction(function () use ($request, $validated) {
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('employees/avatars', 'public');
            }

            // Auto generate next employee code
            $lastEmployee = Employee::withTrashed()->latest('id')->first();
            $nextId = $lastEmployee ? ($lastEmployee->id + 1) : 1;
            $generatedCode = sprintf('EMP-%04d', $nextId);

            // 1. Generate Corporate Email: firstname.secondname@astroleaderskw.com
            $corporateEmail = $this->generateCorporateEmail(
                $validated['name_en'] ?? '',
                $validated['name_ar'] ?? ''
            );

            // 2. Link existing User Account by phone or email, or create a brand new one
            $phoneInput = $validated['phone'];
            $existingUserByPhone = User::where('phone', $phoneInput)->first();
            $existingUserByEmail = User::where('email', $corporateEmail)->first();
            $user = $existingUserByPhone ?: $existingUserByEmail;

            $defaultPassword = 'astro@123';
            if ($user) {
                // Safely update existing user details without breaking unique phone constraint
                $userUpdateData = [
                    'name' => !empty($validated['name_en']) ? $validated['name_en'] : $validated['name_ar'],
                ];
                if (!$existingUserByEmail || $existingUserByEmail->id === $user->id) {
                    $userUpdateData['email'] = $corporateEmail;
                }
                $user->update($userUpdateData);
            } else {
                $user = User::create([
                    'name'      => !empty($validated['name_en']) ? $validated['name_en'] : $validated['name_ar'],
                    'email'     => $corporateEmail,
                    'phone'     => $phoneInput,
                    'password'  => Hash::make($defaultPassword),
                    'user_type' => UserType::EMPLOYEE,
                    'is_active' => true,
                ]);
            }

            if (method_exists($user, 'assignRole') && !$user->hasAnyRole(Role::all())) {
                $user->assignRole('employee');
            }

            // Create Employee Record with default Pending/Active status and NO SALARY
            $employee = Employee::create([
                'employee_code'               => $generatedCode,
                'user_id'                     => $user->id,
                'primary_branch_id'           => (int) $validated['primary_branch_id'],
                'avatar'                      => $avatarPath,
                'gender'                      => $validated['gender'],
                'marital_status'              => $validated['marital_status'] ?? null,
                'date_of_birth'               => $validated['date_of_birth'] ?? null,
                'national_id'                 => $validated['national_id'],
                'nationality'                 => $validated['nationality'] ?? null,
                'blood_type'                  => $validated['blood_type'] ?? null,
                'hire_date'                   => now()->toDateString(),
                'status'                      => 'active',
                'employment_type'             => 'full_time',
                'phone'                       => $validated['phone'],
                'alternate_phone'             => $validated['alternate_phone'] ?? null,
                'email'                       => $corporateEmail,
                'governorate_id'              => !empty($validated['governorate_id']) ? (int) $validated['governorate_id'] : null,
                'city'                        => $validated['city'] ?? null,
                'emergency_contact_name'      => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_relation'  => $validated['emergency_contact_relation'] ?? null,
                'emergency_contact_phone'     => $validated['emergency_contact_phone'] ?? null,
                'bank_id'                     => !empty($validated['bank_id']) ? (int) $validated['bank_id'] : null,
                'account_holder_name'         => $validated['account_holder_name'] ?? null,
                'account_number'              => $validated['account_number'] ?? null,
                'iban'                        => $validated['iban'] ?? null,
                'basic_salary'                => 0, // Salary is explicitly 0 and set only by HR admin
                'daily_working_hours'         => 8.0,
                'onboarding_token'            => \Illuminate\Support\Str::random(40),
                'onboarding_completed_at'     => now(),
            ]);

            // Save translations
            $employee->translateOrNew('ar')->name = $validated['name_ar'];
            $employee->translateOrNew('ar')->address_1 = $validated['address_1_ar'] ?? null;
            if (!empty($validated['name_en'])) {
                $employee->translateOrNew('en')->name = $validated['name_en'];
            }
            $employee->save();

            // Save uploaded documents linked to document_type_id
            $documentsInput = $request->file('documents', []);
            if (is_array($documentsInput)) {
                foreach ($documentsInput as $docTypeId => $fileArray) {
                    if (!empty($fileArray['file']) && $fileArray['file']->isValid()) {
                        $file = $fileArray['file'];
                        $path = $file->store('employees/documents', 'public');
                        EmployeeDocument::create([
                            'employee_id'      => $employee->id,
                            'document_type_id' => (int) $docTypeId,
                            'file_path'        => $path,
                            'file_name'        => $file->getClientOriginalName(),
                        ]);
                    }
                }
            }

            // Fallback for legacy static file inputs if any
            $legacyDocTypes = [
                'doc_national_id' => 5, // Civil ID / Residence
                'doc_certificate' => 8, // Qualification Certificate
            ];
            foreach ($legacyDocTypes as $inputKey => $docTypeId) {
                if ($request->hasFile($inputKey)) {
                    $file = $request->file($inputKey);
                    $path = $file->store('employees/documents', 'public');
                    EmployeeDocument::create([
                        'employee_id'      => $employee->id,
                        'document_type_id' => $docTypeId,
                        'file_path'        => $path,
                        'file_name'        => $file->getClientOriginalName(),
                    ]);
                }
            }

            return $employee;
        });

        return view('hr::onboarding_portal.success', compact('employee'));
    }

    /**
     * Display the employee self-onboarding form.
     */
    public function show(string $token): View
    {
        if (!session()->has('locale')) {
            session()->put('locale', 'en');
            app()->setLocale('en');
        }

        $employee = Employee::where('onboarding_token', $token)->firstOrFail();
        $formData = $this->employeeService->getFormData();

        return view('hr::onboarding_portal.form', array_merge($formData, compact('employee')));
    }

    /**
     * Submit and update employee details directly from the onboarding form.
     */
    public function update(Request $request, string $token): RedirectResponse|View
    {
        $employee = Employee::where('onboarding_token', $token)->firstOrFail();

        $validated = $request->validate([
            'name_ar'                  => ['required', 'string', 'max:255'],
            'name_en'                  => ['nullable', 'string', 'max:255'],
            'primary_branch_id'        => ['required', 'integer', 'exists:nursery_branches,id'],
            'gender'                   => ['required', 'string', 'in:male,female'],
            'marital_status'           => ['nullable', 'string', 'max:50'],
            'date_of_birth'            => ['nullable', 'date'],
            'national_id'              => ['required', 'string', 'max:50', 'unique:employees,national_id,' . $employee->id],
            'nationality'              => ['nullable', 'string', 'max:50'],
            'blood_type'               => ['nullable', 'string', 'max:5'],
            'phone'                    => ['required', 'string', 'max:30'],
            'alternate_phone'          => ['nullable', 'string', 'max:30'],
            'email'                    => ['nullable', 'string', 'max:100'],
            'city'                     => ['nullable', 'string', 'max:100'],
            'address_1_ar'             => ['nullable', 'string', 'max:255'],
            'emergency_contact_name'   => ['nullable', 'string', 'max:100'],
            'emergency_contact_relation'=> ['nullable', 'string', 'max:50'],
            'emergency_contact_phone'  => ['nullable', 'string', 'max:30'],
            'account_holder_name'      => ['nullable', 'string', 'max:150'],
            'account_number'           => ['nullable', 'string', 'max:50'],
            'iban'                     => ['nullable', 'string', 'max:50'],
            'avatar'                   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'documents'                => ['nullable', 'array'],
            'documents.*.file'         => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg,webp', 'max:5120'],
            'doc_national_id'          => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
            'doc_certificate'          => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
            'doc_other'                => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
        ]);

        DB::transaction(function () use ($request, $employee, $validated) {
            // Update Avatar if uploaded
            if ($request->hasFile('avatar')) {
                $validated['avatar'] = $request->file('avatar')->store('employees/avatars', 'public');
            }

            // Determine corporate email: either retain existing or generate anew if blank or updated
            $currentEmail = $employee->email;
            if (empty($currentEmail) || !str_ends_with(strtolower($currentEmail), '@astroleaderskw.com')) {
                $currentEmail = $this->generateCorporateEmail(
                    $validated['name_en'] ?? '',
                    $validated['name_ar'] ?? '',
                    $employee->id,
                    $employee->user_id
                );
            }

            // Sync User or Create if not linked yet
            $phoneInput = $validated['phone'];
            $user = $employee->user;
            if ($user) {
                $userUpdateData = [
                    'name'  => !empty($validated['name_en']) ? $validated['name_en'] : $validated['name_ar'],
                    'email' => $currentEmail,
                ];
                // Only update phone if not conflicting with another user
                $phoneOwner = User::where('phone', $phoneInput)->where('id', '!=', $user->id)->first();
                if (!$phoneOwner) {
                    $userUpdateData['phone'] = $phoneInput;
                }
                $user->update($userUpdateData);
            } else {
                $existingUserByPhone = User::where('phone', $phoneInput)->first();
                if ($existingUserByPhone) {
                    $user = $existingUserByPhone;
                    $user->update([
                        'name'  => !empty($validated['name_en']) ? $validated['name_en'] : $validated['name_ar'],
                        'email' => $currentEmail,
                    ]);
                } else {
                    $defaultPassword = 'astro@123';
                    $user = User::create([
                        'name'      => !empty($validated['name_en']) ? $validated['name_en'] : $validated['name_ar'],
                        'email'     => $currentEmail,
                        'phone'     => $phoneInput,
                        'password'  => Hash::make($defaultPassword),
                        'user_type' => UserType::EMPLOYEE,
                        'is_active' => true,
                    ]);
                    if (method_exists($user, 'assignRole')) {
                        $user->assignRole('employee');
                    }
                }
            }

            // Update Employee record
            $employee->update([
                'user_id'                    => $user->id,
                'primary_branch_id'          => !empty($validated['primary_branch_id']) ? (int) $validated['primary_branch_id'] : $employee->primary_branch_id,
                'avatar'                     => $validated['avatar'] ?? $employee->avatar,
                'gender'                     => $validated['gender'],
                'marital_status'             => $validated['marital_status'] ?? null,
                'date_of_birth'              => $validated['date_of_birth'] ?? null,
                'national_id'                => $validated['national_id'],
                'nationality'                => $validated['nationality'] ?? null,
                'blood_type'                 => $validated['blood_type'] ?? null,
                'phone'                      => $validated['phone'],
                'alternate_phone'            => $validated['alternate_phone'] ?? null,
                'email'                      => $currentEmail,
                'city'                       => $validated['city'] ?? null,
                'emergency_contact_name'     => $validated['emergency_contact_name'] ?? null,
                'emergency_contact_relation' => $validated['emergency_contact_relation'] ?? null,
                'emergency_contact_phone'    => $validated['emergency_contact_phone'] ?? null,
                'account_holder_name'        => $validated['account_holder_name'] ?? null,
                'account_number'             => $validated['account_number'] ?? null,
                'iban'                       => $validated['iban'] ?? null,
                'onboarding_completed_at'    => now(),
            ]);

            // Update Translations
            $employee->translateOrNew('ar')->name = $validated['name_ar'];
            $employee->translateOrNew('ar')->address_1 = $validated['address_1_ar'] ?? null;
            if (!empty($validated['name_en'])) {
                $employee->translateOrNew('en')->name = $validated['name_en'];
            }
            $employee->save();

            // Handle documents upload to employee_documents linked to document_type_id
            $documentsInput = $request->file('documents', []);
            if (is_array($documentsInput)) {
                foreach ($documentsInput as $docTypeId => $fileArray) {
                    if (!empty($fileArray['file']) && $fileArray['file']->isValid()) {
                        $file = $fileArray['file'];
                        $path = $file->store('employees/documents', 'public');
                        EmployeeDocument::updateOrCreate(
                            [
                                'employee_id'      => $employee->id,
                                'document_type_id' => (int) $docTypeId,
                            ],
                            [
                                'file_path' => $path,
                                'file_name' => $file->getClientOriginalName(),
                            ]
                        );
                    }
                }
            }

            // Fallback for legacy static file inputs if any
            $legacyDocTypes = [
                'doc_national_id' => 5, // Civil ID / Residence
                'doc_certificate' => 8, // Qualification Certificate
            ];
            foreach ($legacyDocTypes as $inputKey => $docTypeId) {
                if ($request->hasFile($inputKey)) {
                    $file = $request->file($inputKey);
                    $path = $file->store('employees/documents', 'public');
                    EmployeeDocument::updateOrCreate(
                        [
                            'employee_id'      => $employee->id,
                            'document_type_id' => $docTypeId,
                        ],
                        [
                            'file_path' => $path,
                            'file_name' => $file->getClientOriginalName(),
                        ]
                    );
                }
            }
        });

        return view('hr::onboarding_portal.success', compact('employee'));
    }

    /**
     * Generate corporate email: firstname.secondname@astroleaderskw.com
     */
    protected function generateCorporateEmail(string $nameEn, string $nameAr, ?int $excludeEmployeeId = null, ?int $excludeUserId = null): string
    {
        $domain = 'astroleaderskw.com';
        $firstName = '';
        $secondName = '';

        // Priority 1: English name parts if supplied
        $cleanEn = trim(preg_replace('/[^a-zA-Z\s]/', '', $nameEn));
        $enParts = array_values(array_filter(explode(' ', $cleanEn)));

        if (count($enParts) >= 2) {
            $firstName = strtolower($enParts[0]);
            $secondName = strtolower($enParts[1]);
        } elseif (count($enParts) === 1) {
            $firstName = strtolower($enParts[0]);
        }

        // Priority 2: Transliterate Arabic name if English parts are missing or incomplete
        if (empty($firstName) || empty($secondName)) {
            $arTrans = '';
            if (function_exists('transliterator_transliterate')) {
                $arTrans = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $nameAr);
            }
            if (empty($arTrans)) {
                $arTrans = Str::ascii($nameAr);
            }
            $cleanAr = trim(preg_replace('/[^a-zA-Z\s]/', '', $arTrans));
            $arParts = array_values(array_filter(explode(' ', $cleanAr)));

            if (empty($firstName) && !empty($arParts[0])) {
                $firstName = strtolower($arParts[0]);
            }
            if (empty($secondName) && !empty($arParts[1])) {
                $secondName = strtolower($arParts[1]);
            }
        }

        // Fallbacks
        $firstName = !empty($firstName) ? Str::slug($firstName, '') : 'employee';
        $secondName = !empty($secondName) ? Str::slug($secondName, '') : 'team';

        $baseLocal = $firstName . '.' . $secondName;
        $finalLocal = $baseLocal;
        $counter = 1;

        // Ensure unique email across employees and users
        while (
            Employee::where('email', $finalLocal . '@' . $domain)
                ->when($excludeEmployeeId, fn($q) => $q->where('id', '!=', $excludeEmployeeId))
                ->exists()
            || User::where('email', $finalLocal . '@' . $domain)
                ->when($excludeUserId, fn($q) => $q->where('id', '!=', $excludeUserId))
                ->exists()
        ) {
            $counter++;
            $finalLocal = $baseLocal . $counter;
        }

        return $finalLocal . '@' . $domain;
    }
}

