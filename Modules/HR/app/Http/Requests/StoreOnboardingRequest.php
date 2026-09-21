<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOnboardingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'candidate_id'       => 'required|exists:hr_candidates,id',
            'job_application_id' => 'nullable|exists:hr_job_applications,id',
            'job_offer_id'       => 'nullable|exists:hr_job_offers,id',
            'department_id'      => 'nullable|exists:departments,id',
            'job_title_id'       => 'nullable|exists:job_titles,id',
            'buddy_employee_id'  => 'nullable|exists:employees,id',
            'start_date'         => 'required|date',
            'notes'              => 'nullable|string',
        ];
    }
}
