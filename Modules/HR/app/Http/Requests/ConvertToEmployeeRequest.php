<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConvertToEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_ar'         => 'required|string|max:255',
            'name_en'         => 'nullable|string|max:255',
            'employee_code'   => 'nullable|string|max:50|unique:employees,employee_code',
            'department_id'   => 'required|exists:departments,id',
            'job_title_id'    => 'required|exists:job_titles,id',
            'basic_salary'    => 'required|numeric|min:0',
            'hire_date'       => 'required|date',
            'email'           => 'nullable|email|max:255',
            'phone'           => 'nullable|string|max:50',
            'national_id'     => 'nullable|string|max:50',
            'nationality'     => 'nullable|string|max:100',
            'gender'          => 'required|string',
            'employment_type' => 'required|string',
        ];
    }
}
