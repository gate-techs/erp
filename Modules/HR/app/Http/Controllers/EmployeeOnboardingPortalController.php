<?php

namespace Modules\HR\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeDocument;
use Modules\HR\Services\EmployeeService;

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
            'gender'                   => ['required', 'string', 'in:male,female'],
            'marital_status'           => ['nullable', 'string', 'max:50'],
            'date_of_birth'            => ['nullable', 'date'],
            'national_id'              => ['required', 'string', 'max:50', 'unique:employees,national_id'],
            'nationality'              => ['nullable', 'string', 'max:50'],
            'blood_type'               => ['nullable', 'string', 'max:5'],
            'phone'                    => ['required', 'string', 'max:30'],
            'alternate_phone'          => ['nullable', 'string', 'max:30'],
            'email'                    => ['required', 'email', 'max:100', 'unique:employees,email'],
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

            // Create Employee Record with default Pending/Active status and NO SALARY
            $employee = Employee::create([
                'employee_code'               => $generatedCode,
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
                'email'                       => $validated['email'],
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

            // Save uploaded documents
            $docTypes = [
                'doc_national_id' => 'National ID / Passport',
                'doc_certificate' => 'Graduation Certificate',
                'doc_other'       => 'Resume / Other Document',
            ];

            foreach ($docTypes as $inputKey => $docTitle) {
                if ($request->hasFile($inputKey)) {
                    $file = $request->file($inputKey);
                    $path = $file->store('employees/documents', 'public');
                    EmployeeDocument::create([
                        'employee_id'   => $employee->id,
                        'document_type' => 'other',
                        'title'         => $docTitle,
                        'file_path'     => $path,
                        'file_name'     => $file->getClientOriginalName(),
                        'file_size'     => $file->getSize(),
                        'mime_type'     => $file->getClientMimeType(),
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
            'gender'                   => ['required', 'string', 'in:male,female'],
            'marital_status'           => ['nullable', 'string', 'max:50'],
            'date_of_birth'            => ['nullable', 'date'],
            'national_id'              => ['required', 'string', 'max:50', 'unique:employees,national_id,' . $employee->id],
            'nationality'              => ['nullable', 'string', 'max:50'],
            'blood_type'               => ['nullable', 'string', 'max:5'],
            'phone'                    => ['required', 'string', 'max:30'],
            'alternate_phone'          => ['nullable', 'string', 'max:30'],
            'email'                    => ['required', 'email', 'max:100'],
            'city'                     => ['nullable', 'string', 'max:100'],
            'address_1_ar'             => ['nullable', 'string', 'max:255'],
            'emergency_contact_name'   => ['nullable', 'string', 'max:100'],
            'emergency_contact_relation'=> ['nullable', 'string', 'max:50'],
            'emergency_contact_phone'  => ['nullable', 'string', 'max:30'],
            'account_holder_name'      => ['nullable', 'string', 'max:150'],
            'account_number'           => ['nullable', 'string', 'max:50'],
            'iban'                     => ['nullable', 'string', 'max:50'],
            'avatar'                   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'doc_national_id'          => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
            'doc_certificate'          => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
            'doc_other'                => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg', 'max:5120'],
        ]);

        DB::transaction(function () use ($request, $employee, $validated) {
            // Update Avatar if uploaded
            if ($request->hasFile('avatar')) {
                $validated['avatar'] = $request->file('avatar')->store('employees/avatars', 'public');
            }

            // Sync User email if employee has linked user
            if ($employee->user) {
                $employee->user->update([
                    'name'  => $validated['name_ar'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                ]);
            }

            // Update Employee record
            $employee->update([
                'avatar'                     => $validated['avatar'] ?? $employee->avatar,
                'gender'                     => $validated['gender'],
                'marital_status'             => $validated['marital_status'] ?? null,
                'date_of_birth'              => $validated['date_of_birth'] ?? null,
                'national_id'                => $validated['national_id'],
                'nationality'                => $validated['nationality'] ?? null,
                'blood_type'                 => $validated['blood_type'] ?? null,
                'phone'                      => $validated['phone'],
                'alternate_phone'            => $validated['alternate_phone'] ?? null,
                'email'                      => $validated['email'],
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

            // Handle documents upload to employee_documents
            $docTypes = [
                'doc_national_id' => 'National ID / Passport',
                'doc_certificate' => 'Graduation Certificate',
                'doc_other'       => 'Resume / Other Document',
            ];

            foreach ($docTypes as $inputKey => $docTitle) {
                if ($request->hasFile($inputKey)) {
                    $file = $request->file($inputKey);
                    $path = $file->store('employees/documents', 'public');
                    EmployeeDocument::create([
                        'employee_id'   => $employee->id,
                        'document_type' => 'other',
                        'title'         => $docTitle,
                        'file_path'     => $path,
                        'file_name'     => $file->getClientOriginalName(),
                        'file_size'     => $file->getSize(),
                        'mime_type'     => $file->getClientMimeType(),
                    ]);
                }
            }
        });

        return view('hr::onboarding_portal.success', compact('employee'));
    }
}

