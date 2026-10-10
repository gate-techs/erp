<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIncidentReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'student_id'          => ['required', 'integer', 'exists:academic_students,id'],
            'incident_type_id'    => ['nullable', 'integer', 'exists:general_settings,id'],
            'user_id'             => ['nullable', 'integer', 'exists:users,id'],
            'date'                => ['required', 'date'],
            'time'                => ['nullable', 'string', 'max:20'],
            'location'            => ['nullable', 'string', 'max:255'],
            'student_description' => ['nullable', 'string'],
            'procedures'          => ['nullable', 'string'],
            'persons'             => ['nullable', 'array'],
            'persons.*'           => ['integer', 'exists:users,id'],
            'nurse_report'        => ['nullable', 'string'],
            'institution_id'      => ['nullable', 'integer', 'exists:nurseries,id'],
            'branch_id'           => ['nullable', 'integer', 'exists:nursery_branches,id'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'student_id'          => __('Student'),
            'incident_type_id'    => __('Incident Type'),
            'user_id'             => __('Responsible Officer'),
            'date'                => __('Date'),
            'time'                => __('Time'),
            'location'            => __('Location'),
            'student_description' => __('Student Description'),
            'procedures'          => __('Procedures Taken'),
            'persons'             => __('Witnesses / Present Staff'),
            'nurse_report'        => __('Nurse Report'),
            'institution_id'      => __('Nursery'),
            'branch_id'           => __('Branch'),
        ];
    }
}
