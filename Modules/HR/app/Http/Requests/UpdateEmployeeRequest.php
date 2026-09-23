<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HR\Enums\EmployeeStatus;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\Gender;
use Modules\HR\Enums\MaritalStatus;

class UpdateEmployeeRequest extends FormRequest
{
    //======================
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    //======================
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $employeeId = $this->route('employee')?->id ?? $this->route('employee') ?? $this->employee;

        return [
            // Code, Avatar & Academic Teacher Flag
            'employee_code'               => ['nullable', 'string', 'max:50', Rule::unique('employees', 'employee_code')->ignore($employeeId)],
            'is_teacher'                  => ['nullable', 'boolean'],
            'avatar'                      => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],

            // Names (Full Name)
            'name_ar'                     => ['required', 'string', 'max:255'],
            'name_en'                     => ['nullable', 'string', 'max:255'],

            // 1. Personal & Civil Info
            'gender'                      => ['required', 'string', 'max:50'],
            'marital_status'              => ['nullable', 'string', 'max:50'],
            'religion'                    => ['nullable', 'string', 'max:50'],
            'nationality'                 => ['nullable', 'string', 'max:50'],
            'date_of_birth'               => ['nullable', 'date'],
            'national_id'                 => ['nullable', 'string', 'max:50', Rule::unique('employees', 'national_id')->ignore($employeeId)],
            'blood_type'                  => ['nullable', 'string', 'max:5'],
            'visa_type_id'                => ['nullable', 'integer', 'exists:hr_settings,id'],
            'visa_expiry_date'            => ['nullable', 'date'],
            'passport_number'             => ['nullable', 'string', 'max:50'],
            'passport_issue_date'         => ['nullable', 'date'],
            'passport_expiry_date'        => ['nullable', 'date'],

            // 2. Job & Department Assignment
            'primary_branch_id'           => ['nullable', 'integer', 'exists:nursery_branches,id'],
            'branches'                    => ['nullable', 'array'],
            'branches.*'                  => ['integer', 'exists:nursery_branches,id'],
            'department_id'               => ['nullable', 'integer', 'exists:departments,id'],
            'job_title_id'                => ['nullable', 'integer', 'exists:job_titles,id'],
            'manager_id'                  => ['nullable', 'integer', 'exists:employees,id', Rule::notIn([$employeeId])],
            'work_schedule_id'            => ['nullable', 'integer', 'exists:work_schedules,id'],
            'shift_id'                    => ['nullable', 'integer', 'exists:shifts,id'],
            'employment_type'             => ['required', Rule::in(EmploymentType::values())],
            'hire_date'                   => ['required', 'date'],
            'probation_end_date'          => ['nullable', 'date'],
            'contract_end_date'           => ['nullable', 'date'],
            'status'                      => ['required', Rule::in(EmployeeStatus::values())],

            // 3. Contact & Location
            'phone'                       => ['required', 'string', 'max:30'],
            'alternate_phone'             => ['nullable', 'string', 'max:30'],
            'email'                       => ['nullable', 'email', 'max:100'],
            'governorate_id'              => ['nullable', 'integer', 'exists:general_settings,id'],
            'city'                        => ['nullable', 'string', 'max:100'],
            'address_1_ar'                => ['nullable', 'string', 'max:255'],
            'address_1_en'                => ['nullable', 'string', 'max:255'],
            'address_2_ar'                => ['nullable', 'string', 'max:255'],
            'address_2_en'                => ['nullable', 'string', 'max:255'],

            // 4. Emergency Contact
            'emergency_contact_name'      => ['nullable', 'string', 'max:100'],
            'emergency_contact_relation'  => ['nullable', 'string', 'max:50'],
            'emergency_contact_phone'     => ['nullable', 'string', 'max:30'],

            // 5. Bank & Financial Setup
            'bank_id'                     => ['nullable', 'integer', 'exists:banks,id'],
            'bank_branch_id'              => ['nullable', 'integer', 'exists:banks,id'],
            'account_holder_name'         => ['nullable', 'string', 'max:150'],
            'account_number'              => ['nullable', 'string', 'max:50'],
            'iban'                        => ['nullable', 'string', 'max:50'],
            'basic_salary'                => ['nullable', 'numeric', 'min:0'],
            'hourly_rate'                 => ['nullable', 'numeric', 'min:0'],
            'daily_working_hours'         => ['nullable', 'numeric', 'min:0', 'max:24'],
            'social_insurance_number'     => ['nullable', 'string', 'max:50'],
            'social_insurance_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes_ar'                    => ['nullable', 'string'],
            'notes_en'                    => ['nullable', 'string'],

            // 6. User Account
            'create_user_account'         => ['nullable', 'boolean'],
            'user_id'                     => ['nullable', 'integer', 'exists:users,id'],
            'user_password'               => ['nullable', 'string', 'min:6'],
            'role'                        => ['nullable', 'string', 'exists:roles,name'],

            // 7. Allowances
            'allowances'                  => ['nullable', 'array'],
            'allowances.*.allowance_type_id' => ['required_with:allowances', 'integer', 'exists:hr_settings,id'],
            'allowances.*.amount'         => ['required_with:allowances', 'numeric', 'min:0'],
            'allowances.*.is_percentage'  => ['nullable', 'boolean'],
            'allowances.*.is_recurring'   => ['nullable', 'boolean'],
            'allowances.*.effective_date' => ['nullable', 'date'],
            'allowances.*.notes'          => ['nullable', 'string', 'max:255'],

            // 8. Documents
            'documents'                   => ['nullable', 'array'],
            'documents.*.document_type_id'=> ['nullable', 'integer', 'exists:hr_settings,id'],
            'documents.*.document_number' => ['nullable', 'string', 'max:100'],
            'documents.*.file'            => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg,doc,docx', 'max:10240'],
            'documents.*.file_path'       => ['nullable', 'string'],
            'documents.*.file_name'       => ['nullable', 'string'],
            'documents.*.issue_date'      => ['nullable', 'date'],
            'documents.*.expiry_date'     => ['nullable', 'date'],
            'documents.*.notify_before_days' => ['nullable', 'integer', 'min:1', 'max:365'],
            'documents.*.notes'           => ['nullable', 'string'],
        ];
    }

    //======================
    /**
     * Custom attributes labels for validation messages.
     */
    public function attributes(): array
    {
        return [
            'employee_code'       => __('Employee Code'),
            'avatar'              => __('Avatar'),
            'name_ar'             => __('Full Name [Arabic]'),
            'name_en'             => __('Full Name [English]'),
            'gender'              => __('Gender'),
            'marital_status'      => __('Marital Status'),
            'nationality'         => __('Nationality'),
            'date_of_birth'       => __('Date of Birth'),
            'national_id'         => __('National ID'),
            'blood_type'          => __('Blood Type'),
            'primary_branch_id'   => __('Primary Branch'),
            'department_id'       => __('Department'),
            'job_title_id'        => __('Job Title'),
            'manager_id'          => __('Direct Manager'),
            'work_schedule_id'    => __('Work Schedule'),
            'shift_id'            => __('Shift'),
            'employment_type'     => __('Employment Type'),
            'hire_date'           => __('Hire Date'),
            'status'              => __('Status'),
            'phone'               => __('Phone Number'),
            'email'               => __('Email'),
            'basic_salary'        => __('Basic Salary'),
            'daily_working_hours' => __('Daily Working Hours'),
        ];
    }
}
