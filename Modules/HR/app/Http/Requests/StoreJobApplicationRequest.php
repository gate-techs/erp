<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_opening_id'         => ['required', 'exists:hr_job_openings,id'],
            'first_name'             => ['required', 'string', 'max:100'],
            'last_name'              => ['nullable', 'string', 'max:100'],
            'email'                  => ['required', 'email', 'max:255'],
            'phone'                  => ['nullable', 'string', 'max:30'],
            'nationality'            => ['nullable', 'string', 'max:100'],
            'city'                   => ['nullable', 'string', 'max:100'],
            'current_job_title'      => ['nullable', 'string', 'max:150'],
            'current_company'        => ['nullable', 'string', 'max:150'],
            'total_experience_years' => ['nullable', 'numeric', 'min:0', 'max:50'],
            'expected_salary'        => ['nullable', 'numeric', 'min:0'],
            'currency'               => ['nullable', 'string', 'max:10'],
            'notice_period_days'     => ['nullable', 'integer', 'min:0'],
            'source'                 => ['nullable', 'string', 'max:50'],
            'stage'                  => ['nullable', 'string', 'in:new,screening,interview,offer,hired'],
            'linkedin_url'           => ['nullable', 'url', 'max:500'],
            'portfolio_url'          => ['nullable', 'url', 'max:500'],
            'resume'                 => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'], // 10MB
            'cover_letter'           => ['nullable', 'string'],
            'notes'                  => ['nullable', 'string'],
        ];
    }
}
