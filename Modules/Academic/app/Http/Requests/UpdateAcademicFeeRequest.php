<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAcademicFeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institution_id'       => ['sometimes', 'required', 'integer', 'exists:nurseries,id'],
            'branch_id'            => ['nullable', 'integer', 'exists:nursery_branches,id'],
            'academic_year_id'     => ['sometimes', 'required', 'integer', 'exists:academic_years,id'],
            'academic_stage_id'    => ['sometimes', 'required', 'integer', 'exists:academic_stages,id'],
            'academic_fee_type_id' => ['sometimes', 'required', 'integer', 'exists:academic_fee_types,id'],
            'billing_period'       => ['sometimes', 'required', 'string', 'in:one_time,monthly,term,annually'],
            'name_ar'              => ['sometimes', 'required', 'string', 'max:255'],
            'name_en'              => ['sometimes', 'required', 'string', 'max:255'],
            'description_ar'       => ['nullable', 'string'],
            'description_en'       => ['nullable', 'string'],
            'amount'               => ['sometimes', 'required', 'numeric', 'min:0'],
            'status'               => ['sometimes', 'required', 'string', 'in:active,inactive'],
            'image'                => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'institution_id.required'       => __('The educational institution is required.'),
            'academic_year_id.required'     => __('The academic year is required.'),
            'academic_stage_id.required'    => __('The academic stage is required.'),
            'academic_fee_type_id.required' => __('The fee type is required.'),
            'billing_period.required'       => __('The billing period is required.'),
            'name_ar.required'              => __('The fee name in Arabic is required.'),
            'name_en.required'              => __('The fee name in English is required.'),
            'amount.required'               => __('The fee amount is required.'),
            'amount.numeric'                => __('The fee amount must be a valid number.'),
            'amount.min'                    => __('The fee amount cannot be negative.'),
            'status.required'               => __('The status is required.'),
        ];
    }
}
