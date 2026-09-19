<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HR\Enums\LeaveType;

class StoreLeaveRequestRequest extends FormRequest
{
    //======================
    public function authorize(): bool
    {
        return true;
    }

    //======================
    public function rules(): array
    {
        return [
            'employee_id'             => ['required', 'integer', 'exists:employees,id'],
            'leave_type'              => ['required', Rule::enum(LeaveType::class)],
            'start_date'              => ['required', 'date'],
            'end_date'                => ['required', 'date', 'after_or_equal:start_date'],
            'replacement_employee_id' => ['nullable', 'integer', 'exists:employees,id', 'different:employee_id'],
            'reason'                  => ['nullable', 'string', 'max:1000'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:50'],
            'attachment'              => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    //======================
    public function attributes(): array
    {
        return [
            'employee_id'             => __('Employee'),
            'leave_type'              => __('Leave Type'),
            'start_date'              => __('Start Date'),
            'end_date'                => __('End Date'),
            'replacement_employee_id' => __('Replacement Employee'),
            'reason'                  => __('Reason / Notes'),
            'emergency_contact_phone' => __('Emergency Contact Phone'),
            'attachment'              => __('Official Attachment / Medical Certificate'),
        ];
    }
}
