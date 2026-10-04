<?php

namespace Modules\Tasks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskGroupRequest extends FormRequest
{
    //-----------------------------------------
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    //-----------------------------------------
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name_ar'        => ['required', 'string', 'max:255'],
            'name_en'        => ['required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'department_id'  => ['nullable', 'exists:departments,id'],
            'start_date'     => ['nullable', 'date'],
            'end_date'       => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active'      => ['nullable', 'boolean'],
        ];
    }

    //-----------------------------------------
    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name_ar'        => __('Name in Arabic'),
            'name_en'        => __('Name in English'),
            'description_ar' => __('Description in Arabic'),
            'description_en' => __('Description in English'),
            'department_id'  => __('Department'),
            'start_date'     => __('Start Date'),
            'end_date'       => __('End Date'),
            'is_active'      => __('Status'),
        ];
    }
}
