<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HR\Enums\LoanType;

class StoreLoanRequest extends FormRequest
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
            'type'               => ['required', 'string', Rule::enum(LoanType::class)],
            'amount'             => ['required', 'numeric', 'min:1'],
            'installments_count' => ['required', 'integer', 'min:1', 'max:60'],
            'start_date'         => ['required', 'date'],
            'reason'             => ['required', 'string', 'max:1000'],
            'attachment'         => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'employee_id'        => __('Employee'),
            'type'               => __('Loan Type'),
            'amount'             => __('Loan Amount'),
            'installments_count' => __('Installments Count'),
            'start_date'         => __('First Installment Date'),
            'reason'             => __('Reason'),
            'attachment'         => __('Attachment'),
        ];
    }
}
