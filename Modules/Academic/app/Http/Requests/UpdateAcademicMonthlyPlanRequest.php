<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAcademicMonthlyPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institution_id'       => ['required', 'integer', 'exists:nurseries,id'],
            'branch_id'            => ['nullable', 'integer', 'exists:nursery_branches,id'],
            'academic_year_id'     => ['nullable', 'integer', 'exists:academic_years,id'],
            'academic_stage_id'    => ['required', 'integer', 'exists:academic_stages,id'],
            'academic_class_ids'   => ['required', 'array', 'min:1'],
            'academic_class_ids.*' => ['integer', 'exists:academic_classes,id'],
            'title_ar'             => ['required', 'string', 'max:255'],
            'title_en'             => ['required', 'string', 'max:255'],
            'description_ar'       => ['nullable', 'string'],
            'description_en'       => ['nullable', 'string'],
            'content_ar'           => ['nullable', 'string'],
            'content_en'           => ['nullable', 'string'],
            'start_date'           => ['required', 'date'],
            'end_date'             => ['required', 'date', 'after_or_equal:start_date'],
            'status'               => ['required', 'string', 'in:active,inactive'],
            'image'                => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'attachments'          => ['nullable', 'array'],
            'attachments.*'        => ['file', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'institution_id.required'     => __('The educational institution is required.'),
            'academic_stage_id.required'  => __('The academic stage is required.'),
            'academic_class_ids.required' => __('At least one academic class must be selected.'),
            'academic_class_ids.min'      => __('At least one academic class must be selected.'),
            'title_ar.required'           => __('The plan title in Arabic is required.'),
            'title_en.required'           => __('The plan title in English is required.'),
            'start_date.required'         => __('The start date is required.'),
            'end_date.required'           => __('The end date is required.'),
            'end_date.after_or_equal'     => __('The end date must be a date after or equal to start date.'),
        ];
    }
}
