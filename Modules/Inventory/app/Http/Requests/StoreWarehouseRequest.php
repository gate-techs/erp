<?php

namespace Modules\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    //------------------------------------------
    public function rules(): array
    {
        return [
            'entity_id' => ['required', 'integer', 'exists:entities,id'],
            'branch_id' => ['nullable', 'integer', 'exists:nursery_branches,id'],
            'code' => ['required', 'string', 'max:50'],
            'type' => ['required', 'in:main,branch'],
            'status' => ['nullable', 'in:active,inactive'],
            'name_ar' => ['required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'name_en' => ['required', 'string', 'max:255'],
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
