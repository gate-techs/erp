<?php

namespace Modules\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemCategoryRequest extends FormRequest
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
            'code' => ['sometimes', 'required', 'string', 'max:50'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'status' => ['sometimes', 'in:active,inactive'],
            'name_ar' => ['sometimes', 'required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'name_en' => ['sometimes', 'required', 'string', 'max:255'],
            'description_en' => ['nullable', 'string'],
        ];
    }
}
