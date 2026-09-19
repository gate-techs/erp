<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequisitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'workforce_plan_item_id'      => ['nullable', 'exists:hr_workforce_plan_items,id'],
            'department_id'               => ['required', 'exists:departments,id'],
            'branch_id'                   => ['nullable', 'exists:nursery_branches,id'],
            'job_title_id'                => ['required', 'exists:job_titles,id'],
            'requisition_type'            => ['required', 'string'],
            'replacement_for_employee_id' => ['nullable', 'exists:employees,id'],
            'headcount_needed'            => ['required', 'integer', 'min:1'],
            'employment_type'             => ['required', 'string'],
            'expected_start_date'         => ['nullable', 'date'],
            'min_salary'                  => ['nullable', 'numeric', 'min:0'],
            'max_salary'                  => ['nullable', 'numeric', 'gte:min_salary'],
            'required_experience_years'   => ['nullable', 'integer', 'min:0'],
            'education_level'             => ['nullable', 'string', 'max:100'],
            'job_description'             => ['nullable', 'string'],
            'responsibilities'            => ['nullable', 'string'],
            'requirements'                => ['nullable', 'string'],
            'justification'               => ['nullable', 'string'],
            'priority'                    => ['required', 'string'],
        ];
    }
}
