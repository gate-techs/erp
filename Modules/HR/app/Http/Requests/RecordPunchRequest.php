<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HR\Enums\AttendanceSource;
use Modules\HR\Enums\PunchType;

class RecordPunchRequest extends FormRequest
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
            'employee_id' => ['required', 'exists:employees,id'],
            'punch_time'  => ['required', 'date'],
            'punch_type'  => ['required', Rule::in(PunchType::values())],
            'source'      => ['nullable', Rule::in(AttendanceSource::values())],
            'notes'       => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Custom attributes labels.
     */
    public function attributes(): array
    {
        return [
            'employee_id' => __('Employee'),
            'punch_time'  => __('Punch Time'),
            'punch_type'  => __('Punch Type'),
            'source'      => __('Source'),
        ];
    }
}
