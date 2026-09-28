<?php

namespace Modules\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'category_id'   => ['sometimes', 'integer', 'exists:item_categories,id'],
            'unit_id'       => ['nullable', 'integer', 'exists:units,id'],
            'code'          => ['sometimes', 'string', 'max:50'],
            'barcode'       => ['nullable', 'string', 'max:100'],
            'cost_price'    => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'status'        => ['nullable', 'in:active,inactive'],
            'name_ar'       => ['sometimes', 'string', 'max:255'],
            'description_ar'=> ['nullable', 'string'],
            'name_en'       => ['sometimes', 'string', 'max:255'],
            'description_en'=> ['nullable', 'string'],
            'image'         => ['nullable', 'image', 'max:2048'],
        ];
    }
}
