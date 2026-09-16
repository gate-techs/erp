<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HR\Enums\DepartmentStatus;

class StoreDepartmentRequest extends FormRequest
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
            'code'           => ['nullable', 'string', 'max:50', 'unique:departments,code'],
            'name_ar'        => ['required', 'string', 'max:255'],
            'name_en'        => ['required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'nursery_id'     => ['nullable', 'exists:nurseries,id'],
            'branch_id'      => ['nullable', 'exists:nursery_branches,id'],
            'parent_id'      => ['nullable', 'exists:departments,id'],
            'manager_id'     => ['nullable', 'exists:users,id'],
            'status'         => ['required', Rule::in(DepartmentStatus::values())],
        ];
    }

    //======================
    /**
     * Custom attributes labels for validation messages.
     */
    public function attributes(): array
    {
        return [
            'code'           => __('Department Code'),
            'name_ar'        => __('Name in Arabic'),
            'name_en'        => __('Name in English'),
            'description_ar' => __('Description in Arabic'),
            'description_en' => __('Description in English'),
            'nursery_id'     => __('Nursery'),
            'branch_id'      => __('Branch'),
            'parent_id'      => __('Parent Administration / Department'),
            'manager_id'     => __('Responsible Manager'),
            'status'         => __('Status'),
        ];
    }
}
