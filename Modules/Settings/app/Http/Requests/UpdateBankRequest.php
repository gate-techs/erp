<?php

namespace Modules\Settings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBankRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_ar'    => ['required', 'string', 'max:255'],
            'name_en'    => ['required', 'string', 'max:255'],
            'code'       => ['nullable', 'string', 'max:50'],
            'swift_code' => ['nullable', 'string', 'max:50'],
            'status'     => ['required', 'in:active,inactive'],
            'address_ar' => ['nullable', 'string', 'max:500'],
            'address_en' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name_ar'    => __('Bank Name (Arabic)'),
            'name_en'    => __('Bank Name (English)'),
            'code'       => __('Bank Code'),
            'swift_code' => __('SWIFT Code'),
            'status'     => __('Status'),
            'address_ar' => __('Address (Arabic)'),
            'address_en' => __('Address (English)'),
        ];
    }
}
