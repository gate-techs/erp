<?php

namespace Modules\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'symbol'  => ['sometimes', 'string', 'max:20'],
            'status'  => ['nullable', 'in:active,inactive'],
            'name_ar' => ['sometimes', 'string', 'max:100'],
            'name_en' => ['sometimes', 'string', 'max:100'],
        ];
    }
}
