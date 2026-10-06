<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Academic\Models\AcademicSubject;

class StoreAcademicSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institution_id' => ['required', 'integer', 'min:1'],
            'academic_stages' => ['required', 'array', 'min:1'],
            'academic_stages.*' => ['integer', 'exists:academic_stages,id'],
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'name_ar' => ['required', 'string', 'max:150'],
            'name_en' => ['required', 'string', 'max:150'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'image' => ['nullable'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240'],
            'status' => ['nullable', Rule::in(AcademicSubject::statuses())],
        ];
    }

    public function attributes(): array
    {
        return [
            'institution_id' => __('Institution'),
            'academic_stages' => __('Academic Stages'),
            'academic_stages.*' => __('Academic Stage'),
            'academic_year_id' => __('Academic Year'),
            'name_ar' => __('Subject Name (Arabic)'),
            'name_en' => __('Subject Name (English)'),
            'description_ar' => __('Description (Arabic)'),
            'description_en' => __('Description (English)'),
            'image' => __('Subject Image'),
            'attachments' => __('Attachments'),
            'status' => __('Status'),
        ];
    }
}
