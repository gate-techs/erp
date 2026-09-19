<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePayrollItemRequest extends FormRequest
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
            'basic_salary'        => ['required', 'numeric', 'min:0'],
            'allowances_amount'   => ['nullable', 'numeric', 'min:0'],
            'overtime_amount'     => ['nullable', 'numeric', 'min:0'],
            'rewards_amount'      => ['nullable', 'numeric', 'min:0'],
            'other_earnings'      => ['nullable', 'numeric', 'min:0'],
            'absence_deduction'   => ['nullable', 'numeric', 'min:0'],
            'late_deduction'      => ['nullable', 'numeric', 'min:0'],
            'loans_deduction'     => ['nullable', 'numeric', 'min:0'],
            'penalties_deduction' => ['nullable', 'numeric', 'min:0'],
            'insurance_deduction' => ['nullable', 'numeric', 'min:0'],
            'other_deductions'    => ['nullable', 'numeric', 'min:0'],
            'notes'               => ['nullable', 'string', 'max:500'],
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
            'basic_salary'        => __('Basic Salary'),
            'allowances_amount'   => __('Allowances'),
            'overtime_amount'     => __('Overtime Amount'),
            'rewards_amount'      => __('Rewards'),
            'other_earnings'      => __('Other Earnings'),
            'absence_deduction'   => __('Absence Deduction'),
            'late_deduction'      => __('Late Deduction'),
            'loans_deduction'     => __('Loans Deduction'),
            'penalties_deduction' => __('Penalties Deduction'),
            'insurance_deduction' => __('Social Insurance'),
            'other_deductions'    => __('Other Deductions'),
            'notes'               => __('Notes'),
        ];
    }
}
