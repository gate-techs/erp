<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HR\Enums\ShiftStatus;

class StoreShiftRequest extends FormRequest
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
            'code'             => ['nullable', 'string', 'max:50', 'unique:shifts,code'],
            'name_ar'          => ['required', 'string', 'max:255'],
            'name_en'          => ['required', 'string', 'max:255'],
            'description_ar'   => ['nullable', 'string'],
            'description_en'   => ['nullable', 'string'],
            'start_time'       => ['required', 'date_format:H:i'],
            'end_time'         => ['required', 'date_format:H:i'],
            'checkin_start'    => ['nullable', 'date_format:H:i'],
            'checkin_end'      => ['nullable', 'date_format:H:i'],
            'checkout_start'   => ['nullable', 'date_format:H:i'],
            'checkout_end'     => ['nullable', 'date_format:H:i'],
            'grace_period_in'  => ['nullable', 'integer', 'min:0', 'max:180'],
            'grace_period_out' => ['nullable', 'integer', 'min:0', 'max:180'],
            'is_overnight'     => ['nullable', 'boolean'],
            'color'            => ['nullable', 'string', 'max:20'],
            'status'           => ['required', Rule::in(ShiftStatus::values())],
        ];
    }

    //======================
    /**
     * Custom attributes labels for validation messages.
     */
    public function attributes(): array
    {
        return [
            'code'             => __('Shift Code'),
            'name_ar'          => __('Shift Name [Arabic]'),
            'name_en'          => __('Shift Name [English]'),
            'description_ar'   => __('Description [Arabic]'),
            'description_en'   => __('Description [English]'),
            'start_time'       => __('Start Time'),
            'end_time'         => __('End Time'),
            'checkin_start'    => __('Check-in Start Window'),
            'checkin_end'      => __('Check-in End Window'),
            'checkout_start'   => __('Check-out Start Window'),
            'checkout_end'     => __('Check-out End Window'),
            'grace_period_in'  => __('Late Grace Period (Minutes)'),
            'grace_period_out' => __('Early Leave Grace Period (Minutes)'),
            'is_overnight'     => __('Overnight Shift'),
            'color'            => __('Color'),
            'status'           => __('Status'),
        ];
    }
}
