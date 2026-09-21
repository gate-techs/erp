<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobOpeningRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_requisition_id'   => ['nullable', 'exists:hr_job_requisitions,id'],
            'title'                => ['nullable', 'string', 'max:255'],
            'title_ar'             => ['nullable', 'string', 'max:255'],
            'title_en'             => ['nullable', 'string', 'max:255'],
            'job_title_id'         => ['required', 'exists:job_titles,id'],
            'department_id'        => ['required', 'exists:departments,id'],
            'branch_id'            => ['nullable', 'exists:nursery_branches,id'],
            'employment_type'      => ['required', 'string'],
            'workplace_type'       => ['required', 'string'],
            'posting_type'         => ['required', 'string'],
            'open_positions'       => ['required', 'integer', 'min:1'],
            'min_experience_years' => ['nullable', 'integer', 'min:0'],
            'max_experience_years' => ['nullable', 'integer', 'gte:min_experience_years'],
            'education_level'      => ['nullable', 'string', 'max:255'],
            'education_level_ar'   => ['nullable', 'string', 'max:255'],
            'education_level_en'   => ['nullable', 'string', 'max:255'],
            'min_salary'           => ['nullable', 'numeric', 'min:0'],
            'max_salary'           => ['nullable', 'numeric', 'gte:min_salary'],
            'currency'             => ['nullable', 'string', 'max:10'],
            'hide_salary'          => ['nullable', 'boolean'],
            'publish_date'         => ['nullable', 'date'],
            'closing_date'         => ['nullable', 'date', 'after_or_equal:publish_date'],
            'job_summary'          => ['nullable', 'string'],
            'job_summary_ar'       => ['nullable', 'string'],
            'job_summary_en'       => ['nullable', 'string'],
            'responsibilities'     => ['nullable', 'string'],
            'responsibilities_ar'  => ['nullable', 'string'],
            'responsibilities_en'  => ['nullable', 'string'],
            'requirements'         => ['nullable', 'string'],
            'requirements_ar'      => ['nullable', 'string'],
            'requirements_en'      => ['nullable', 'string'],
            'benefits'             => ['nullable', 'string'],
            'benefits_ar'          => ['nullable', 'string'],
            'benefits_en'          => ['nullable', 'string'],
            'status'               => ['nullable', 'string'],
        ];
    }
}
