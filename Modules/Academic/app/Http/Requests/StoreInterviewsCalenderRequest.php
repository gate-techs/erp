<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInterviewsCalenderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'guardian_civil_id'    => ['nullable', 'string', 'max:30'],
            'guardian_name'        => ['required', 'string', 'max:255'],
            'guardian_phone'       => ['required', 'string', 'max:30'],
            'guardian_email'       => ['nullable', 'email', 'max:100'],
            'institution_id'       => ['required', 'integer', 'exists:nurseries,id'],
            'branch_id'            => ['nullable', 'integer', 'exists:nursery_branches,id'],
            'academic_guardian_id' => ['nullable', 'integer', 'exists:academic_guardians,id'],
            'interview_date'       => ['required', 'date_format:Y-m-d'],
            'start_time'           => ['required', 'date_format:H:i'],
            'end_time'             => ['nullable', 'date_format:H:i'],
            'notes'                => ['nullable', 'string', 'max:2000'],
            'status'               => ['nullable', 'string', 'in:planned,completed,cancelled,absent'],
            'color'                => ['nullable', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'guardian_name.required'  => __('Guardian name is required.'),
            'guardian_phone.required' => __('Phone number is required.'),
            'institution_id.required' => __('Nursery/institution is required.'),
            'interview_date.required' => __('Interview date is required.'),
            'start_time.required'     => __('Start time is required.'),
        ];
    }
}
