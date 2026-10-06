<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Academic\Models\AcademicClass;

class StoreAcademicClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institution_id' => ['required', 'integer', 'min:1'],
            'branch_id' => ['required', 'integer', 'min:1'],
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'academic_stage_id' => ['required', 'integer', 'exists:academic_stages,id'],
            'name_ar' => ['required', 'string', 'max:150'],
            'name_en' => ['required', 'string', 'max:150'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'color' => ['nullable', 'string', 'max:30'],
            'image' => ['nullable'],
            'status' => ['nullable', Rule::in(AcademicClass::statuses())],
        ];
    }

    public function attributes(): array
    {
        return [
            'institution_id' => __('Institution'),
            'branch_id' => __('Branch'),
            'academic_year_id' => __('Academic year'),
            'academic_stage_id' => __('Academic stage'),
            'name_ar' => __('Class name (Arabic)'),
            'name_en' => __('Class name (English)'),
            'description_ar' => __('Description (Arabic)'),
            'description_en' => __('Description (English)'),
            'capacity' => __('Class capacity'),
            'color' => __('Color'),
            'image' => __('Image'),
            'status' => __('Status'),
        ];
    }
}
