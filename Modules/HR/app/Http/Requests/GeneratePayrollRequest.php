<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GeneratePayrollRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'payroll_month' => ['required', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/'],
            'branch_id'     => ['nullable', 'integer', 'exists:nursery_branches,id'],
            'title'         => ['nullable', 'string', 'max:255'],
            'notes'         => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Localized attribute names.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'payroll_month' => __('Payroll Month'),
            'branch_id'     => __('Branch'),
            'title'         => __('Payroll Title'),
            'notes'         => __('Notes'),
        ];
    }
}
