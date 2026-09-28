<?php

namespace Modules\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'category_id'   => ['required', 'integer', 'exists:item_categories,id'],
            'unit_id'       => ['nullable', 'integer', 'exists:units,id'],
            'code'          => ['required', 'string', 'max:50'],
            'barcode'       => ['nullable', 'string', 'max:100'],
            'cost_price'    => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'status'        => ['nullable', 'in:active,inactive'],
            'name_ar'       => ['required', 'string', 'max:255'],
            'description_ar'=> ['nullable', 'string'],
            'name_en'       => ['required', 'string', 'max:255'],
            'description_en'=> ['nullable', 'string'],
            'image'         => ['nullable', 'image', 'max:2048'],
        ];
    }
}
