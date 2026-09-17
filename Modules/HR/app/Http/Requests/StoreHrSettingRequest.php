<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HR\Enums\HrSettingStatus;
use Modules\HR\Enums\HrSettingType;

class StoreHrSettingRequest extends FormRequest
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
            'type'           => ['required', Rule::in(HrSettingType::values())],
            'parent_id'      => ['nullable', 'exists:hr_settings,id'],
            'status'         => ['required', Rule::in(HrSettingStatus::values())],
            'name_ar'        => ['required', 'string', 'max:255'],
            'name_en'        => ['required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
        ];
    }

    //======================
    /**
     * Custom attributes labels for validation messages.
     */
    public function attributes(): array
    {
        return [
            'type'           => __('Setting Type'),
            'parent_id'      => __('Parent Setting'),
            'status'         => __('Status'),
            'name_ar'        => __('Name in Arabic'),
            'name_en'        => __('Name in English'),
            'description_ar' => __('Description in Arabic'),
            'description_en' => __('Description in English'),
        ];
    }
}
