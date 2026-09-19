<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkforcePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'                      => ['required', 'string', 'max:191'],
            'year'                       => ['required', 'integer', 'min:2020', 'max:2100'],
            'start_date'                 => ['required', 'date'],
            'end_date'                   => ['required', 'date', 'after_or_equal:start_date'],
            'notes'                      => ['nullable', 'string'],
            'items'                      => ['required', 'array', 'min:1'],
            'items.*.department_id'      => ['required', 'exists:departments,id'],
            'items.*.branch_id'          => ['nullable', 'exists:nursery_branches,id'],
            'items.*.job_title_id'       => ['required', 'exists:job_titles,id'],
            'items.*.employment_type'    => ['required', 'string'],
            'items.*.target_headcount'   => ['required', 'integer', 'min:1'],
            'items.*.estimated_monthly_salary' => ['required', 'numeric', 'min:0'],
            'items.*.priority'           => ['required', 'string'],
            'items.*.target_quarter'     => ['required', 'string'],
            'items.*.justification'      => ['nullable', 'string'],
        ];
    }
}
