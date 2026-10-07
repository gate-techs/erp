<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Academic\Models\AcademicLesson;

class UpdateAcademicLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institution_id'      => ['required', 'integer', 'min:1'],
            'academic_stage_id'   => ['required', 'integer', 'exists:academic_stages,id'],
            'academic_subject_id' => ['required', 'integer', 'exists:academic_subjects,id'],
            'title_ar'            => ['required', 'string', 'max:255'],
            'title_en'            => ['required', 'string', 'max:255'],
            'content_ar'          => ['nullable', 'string'],
            'content_en'          => ['nullable', 'string'],
            'start_date'          => ['nullable', 'date'],
            'end_date'            => ['nullable', 'date', 'after_or_equal:start_date'],
            'order'               => ['nullable', 'integer', 'min:0'],
            'image'               => ['nullable'],
            'attachments'         => ['nullable', 'array'],
            'attachments.*'       => ['file', 'max:20480'],
            'status'              => ['nullable', Rule::in(AcademicLesson::statuses())],
        ];
    }

    public function attributes(): array
    {
        return [
            'institution_id'      => __('Institution'),
            'academic_stage_id'   => __('Academic Stage'),
            'academic_subject_id' => __('Subject'),
            'title_ar'            => __('Lesson Title (Arabic)'),
            'title_en'            => __('Lesson Title (English)'),
            'content_ar'          => __('Lesson Details (Arabic)'),
            'content_en'          => __('Lesson Details (English)'),
            'start_date'          => __('Start Date'),
            'end_date'            => __('End Date'),
            'order'               => __('Order'),
            'image'               => __('Lesson Image'),
            'attachments'         => __('Attachments'),
            'status'              => __('Status'),
        ];
    }
}
