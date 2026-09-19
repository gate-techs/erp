<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HR\Enums\PenaltyCalculationType;
use Modules\HR\Enums\PenaltyDeductionMethod;

class StorePenaltyRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'employee_id'        => ['required', 'integer', 'exists:employees,id'],
            'penalty_type_id'    => ['required', 'integer', 'exists:hr_settings,id'],
            'calculation_type'   => ['required', 'string', Rule::enum(PenaltyCalculationType::class)],
            'calculation_value'  => [
                Rule::requiredIf(fn() => $this->input('calculation_type') !== PenaltyCalculationType::WARNING_ONLY->value),
                'nullable',
                'numeric',
                'min:0',
            ],
            'penalty_date'       => ['required', 'date'],
            'deduction_method'   => ['required', 'string', Rule::enum(PenaltyDeductionMethod::class)],
            'payroll_month'      => ['nullable', 'string', 'max:7'],
            'reason'             => ['required', 'string', 'max:2000'],
            'notes'              => ['nullable', 'string', 'max:2000'],
            'attachment'         => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'employee_id'       => __('Employee'),
            'penalty_type_id'   => __('Penalty Type'),
            'calculation_type'  => __('Calculation Mode'),
            'calculation_value' => __('Calculation Value'),
            'penalty_date'      => __('Penalty Date'),
            'deduction_method'  => __('Deduction Method'),
            'payroll_month'     => __('Payroll Month'),
            'reason'            => __('Reason & Justification'),
            'notes'             => __('Notes'),
            'attachment'        => __('Supporting Document'),
        ];
    }
}
