<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HR\Enums\JobTitleStatus;

class UpdateJobTitleRequest extends FormRequest
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
        $jobTitleId = $this->route('job_title') ?? $this->route('id');

        return [
            'code'           => ['nullable', 'string', 'max:50', 'unique:job_titles,code,' . $jobTitleId],
            'name_ar'        => ['required', 'string', 'max:255'],
            'name_en'        => ['required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'department_id'  => ['nullable', 'exists:departments,id'],
            'status'         => ['required', Rule::in(JobTitleStatus::values())],
        ];
    }

    //======================
    /**
     * Custom attributes labels for validation messages.
     */
    public function attributes(): array
    {
        return [
            'code'           => __('Job Code'),
            'name_ar'        => __('Name in Arabic'),
            'name_en'        => __('Name in English'),
            'description_ar' => __('Description in Arabic'),
            'description_en' => __('Description in English'),
            'department_id'  => __('Department'),
            'status'         => __('Status'),
        ];
    }
}
