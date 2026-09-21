<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'department_id'            => 'nullable|exists:departments,id',
            'job_title_id'             => 'nullable|exists:job_titles,id',
            'employment_type'          => 'required|string',
            'basic_salary'             => 'required|numeric|min:0',
            'housing_allowance'        => 'nullable|numeric|min:0',
            'transportation_allowance' => 'nullable|numeric|min:0',
            'other_allowances'         => 'nullable|numeric|min:0',
            'currency'                 => 'required|string|max:10',
            'offer_date'               => 'required|date',
            'expiry_date'              => 'nullable|date|after_or_equal:offer_date',
            'joining_date'             => 'nullable|date|after_or_equal:offer_date',
            'probation_months'         => 'required|integer|min:0|max:12',
            'contract_duration_months' => 'nullable|integer|min:1|max:120',
            'terms_and_conditions'     => 'nullable|string',
            'notes'                    => 'nullable|string',
        ];
    }
}
