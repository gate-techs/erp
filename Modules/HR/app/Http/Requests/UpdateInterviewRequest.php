<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\HR\Enums\InterviewMode;
use Modules\HR\Enums\InterviewStatus;
use Modules\HR\Enums\InterviewType;

class UpdateInterviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'             => 'nullable|string|max:255',
            'type'              => ['required', new Enum(InterviewType::class)],
            'mode'              => ['required', new Enum(InterviewMode::class)],
            'status'            => ['required', new Enum(InterviewStatus::class)],
            'scheduled_at'      => 'required|date',
            'duration_minutes'  => 'required|integer|min:15|max:240',
            'location'          => 'nullable|string|max:255',
            'meeting_link'      => 'nullable|url|max:500',
            'notes'             => 'nullable|string',
            'interviewer_ids'   => 'required|array|min:1',
            'interviewer_ids.*' => 'exists:employees,id',
        ];
    }
}
