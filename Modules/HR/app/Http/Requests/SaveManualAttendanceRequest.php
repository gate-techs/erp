<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HR\Enums\AttendanceStatus;

class SaveManualAttendanceRequest extends FormRequest
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
            'employee_id'          => ['required', 'exists:employees,id'],
            'date'                 => ['required', 'date_format:Y-m-d'],
            'check_in'             => ['nullable', 'date_format:H:i'],
            'check_out'            => ['nullable', 'date_format:H:i'],
            'status'               => ['required', Rule::in(AttendanceStatus::values())],
            'total_working_hours'  => ['nullable', 'numeric', 'min:0', 'max:24'],
            'late_minutes'         => ['nullable', 'integer', 'min:0'],
            'early_leave_minutes'  => ['nullable', 'integer', 'min:0'],
            'overtime_minutes'     => ['nullable', 'integer', 'min:0'],
            'adjustment_reason'    => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Custom attributes labels.
     */
    public function attributes(): array
    {
        return [
            'employee_id'          => __('Employee'),
            'date'                 => __('Date'),
            'check_in'             => __('Check-in Time'),
            'check_out'            => __('Check-out Time'),
            'status'               => __('Status'),
            'adjustment_reason'    => __('Adjustment Reason'),
        ];
    }
}
