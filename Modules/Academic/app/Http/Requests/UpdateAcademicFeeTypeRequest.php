<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAcademicFeeTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institution_id' => ['nullable', 'integer', 'exists:nurseries,id'],
            'branch_id'      => ['nullable', 'integer', 'exists:nursery_branches,id'],
            'code'           => ['nullable', 'string', 'max:50'],
            'name_ar'        => ['sometimes', 'required', 'string', 'max:255'],
            'name_en'        => ['sometimes', 'required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'order'          => ['nullable', 'integer', 'min:0'],
            'status'         => ['sometimes', 'required', 'string', 'in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'name_ar.required' => __('The fee type name in Arabic is required.'),
            'name_en.required' => __('The fee type name in English is required.'),
            'status.required'  => __('The status is required.'),
        ];
    }
}
