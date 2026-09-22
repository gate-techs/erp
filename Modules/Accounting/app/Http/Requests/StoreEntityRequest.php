<?php

namespace Modules\Accounting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEntityRequest extends FormRequest
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
            'name_ar'        => ['required', 'string', 'max:255'],
            'name_en'        => ['nullable', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'code'           => ['nullable', 'string', 'max:50', 'unique:entities,code'],
            'type'           => ['required', 'string', 'in:nursery,other'],
            'currency'       => ['required', 'string', 'max:10'],
            'status'         => ['required', 'string', 'in:active,inactive'],
            'nursery_id'     => [
                'nullable',
                'required_if:type,nursery',
                'integer',
                'exists:nurseries,id',
                Rule::unique('entities', 'nursery_id')->whereNull('deleted_at'),
            ],
        ];
    }

    /**
     * Custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'name_ar'        => __('Entity Name (Arabic)'),
            'name_en'        => __('Entity Name (English)'),
            'description_ar' => __('Description (Arabic)'),
            'description_en' => __('Description (English)'),
            'code'           => __('Code'),
            'type'           => __('Type'),
            'currency'       => __('Currency'),
            'status'         => __('Status'),
            'nursery_id'     => __('Nursery'),
        ];
    }
}
