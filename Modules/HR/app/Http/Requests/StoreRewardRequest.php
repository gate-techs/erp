<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HR\Enums\RewardCalculationType;
use Modules\HR\Enums\RewardPaymentMethod;

class StoreRewardRequest extends FormRequest
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
            'reward_type_id'     => ['required', 'integer', 'exists:hr_settings,id'],
            'calculation_type'   => ['required', 'string', Rule::enum(RewardCalculationType::class)],
            'calculation_value'  => ['required', 'numeric', 'min:0.01'],
            'award_date'         => ['required', 'date'],
            'payment_method'     => ['required', 'string', Rule::enum(RewardPaymentMethod::class)],
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
            'reward_type_id'    => __('Reward Type'),
            'calculation_type'  => __('Calculation Mode'),
            'calculation_value' => __('Calculation Value'),
            'award_date'        => __('Award Date'),
            'payment_method'    => __('Payment Method'),
            'payroll_month'     => __('Payroll Month'),
            'reason'            => __('Reason & Justification'),
            'notes'             => __('Notes'),
            'attachment'        => __('Supporting Document'),
        ];
    }
}
