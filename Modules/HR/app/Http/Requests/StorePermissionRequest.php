<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\HR\Enums\PermissionType;

class StorePermissionRequest extends FormRequest
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
            'employee_id'  => ['required', 'integer', 'exists:employees,id'],
            'date'         => ['required', 'date'],
            'type'         => ['required', 'string', 'in:' . implode(',', PermissionType::values())],
            'from_time'    => ['required', 'date_format:H:i'],
            'to_time'      => ['required', 'date_format:H:i', 'after:from_time'],
            'reason'       => ['nullable', 'string', 'max:1000'],
            'is_emergency' => ['nullable', 'boolean'],
            'attachment'   => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5MB
        ];
    }

    /**
     * Custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'employee_id'  => __('Employee'),
            'date'         => __('Permission Date'),
            'type'         => __('Permission Type'),
            'from_time'    => __('Start Time'),
            'to_time'      => __('End Time'),
            'reason'       => __('Reason'),
            'attachment'   => __('Attachment'),
        ];
    }
}
