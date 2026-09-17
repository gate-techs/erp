<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HR\Enums\OfficialHolidayStatus;

class UpdateOfficialHolidayRequest extends FormRequest
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
        return [
            'name_ar'        => ['required', 'string', 'max:255'],
            'name_en'        => ['required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'start_date'     => ['required', 'date'],
            'end_date'       => ['required', 'date', 'after_or_equal:start_date'],
            'is_recurring'   => ['nullable', 'boolean'],
            'branch_id'      => ['nullable', 'integer', 'exists:nursery_branches,id'],
            'color'          => ['nullable', 'string', 'max:20'],
            'status'         => ['required', Rule::in(OfficialHolidayStatus::values())],
        ];
    }

    //======================
    /**
     * Custom attributes labels for validation messages.
     */
    public function attributes(): array
    {
        return [
            'name_ar'        => __('Holiday Name [Arabic]'),
            'name_en'        => __('Holiday Name [English]'),
            'description_ar' => __('Description [Arabic]'),
            'description_en' => __('Description [English]'),
            'start_date'     => __('Start Date'),
            'end_date'       => __('End Date'),
            'is_recurring'   => __('Recurring Annually'),
            'branch_id'      => __('Branch'),
            'color'          => __('Color'),
            'status'         => __('Status'),
        ];
    }
}
