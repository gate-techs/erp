<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\HR\Enums\InterviewMode;
use Modules\HR\Enums\InterviewType;

class StoreInterviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_application_id' => 'required|exists:hr_job_applications,id',
            'title'              => 'nullable|string|max:255',
            'type'               => ['required', new Enum(InterviewType::class)],
            'mode'               => ['required', new Enum(InterviewMode::class)],
            'scheduled_at'       => 'required|date|after_or_equal:now',
            'duration_minutes'   => 'required|integer|min:15|max:240',
            'location'           => 'nullable|string|max:255',
            'meeting_link'       => 'nullable|url|max:500',
            'notes'              => 'nullable|string',
            'interviewer_ids'    => 'required|array|min:1',
            'interviewer_ids.*'  => 'exists:employees,id',
        ];
    }
}
