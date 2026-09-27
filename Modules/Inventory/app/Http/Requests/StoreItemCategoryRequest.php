<?php

namespace Modules\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    //------------------------------------------
    public function rules(): array
    {
        return [
            'parent_id' => ['nullable', 'integer', 'exists:item_categories,id'],
            'code' => ['required', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'in:active,inactive'],
            'name_ar' => ['required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'name_en' => ['required', 'string', 'max:255'],
            'description_en' => ['nullable', 'string'],
        ];
    }
}
