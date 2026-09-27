<?php

namespace Modules\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    //------------------------------------------
    public function rules(): array
    {
        return [
            'entity_id' => ['sometimes', 'required', 'integer', 'exists:entities,id'],
            'branch_id' => ['nullable', 'integer', 'exists:nursery_branches,id'],
            'code' => ['sometimes', 'required', 'string', 'max:50'],
            'type' => ['sometimes', 'required', 'in:main,branch'],
            'status' => ['sometimes', 'in:active,inactive'],
            'name_ar' => ['sometimes', 'required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'name_en' => ['sometimes', 'required', 'string', 'max:255'],
            'description_en' => ['nullable', 'string'],
        ];
    }

    //------------------------------------------
    public function attributes(): array
    {
        return [
            'entity_id' => __('Accounting Entity'),
            'branch_id' => __('Branch'),
            'code' => __('Warehouse Code'),
            'type' => __('Warehouse Type'),
            'status' => __('Status'),
            'name_ar' => __('Warehouse Name [Arabic]'),
            'description_ar' => __('Description [Arabic]'),
            'name_en' => __('Warehouse Name [English]'),
            'description_en' => __('Description [English]'),
        ];
    }
}
