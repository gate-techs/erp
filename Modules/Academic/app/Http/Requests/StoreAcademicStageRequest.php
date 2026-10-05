<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Academic\Models\AcademicStage;

class StoreAcademicStageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institution_id' => ['required', 'integer', 'min:1'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('academic_stages', 'code')
                    ->where(fn ($query) => $query->where('institution_id', $this->integer('institution_id'))),
            ],
            'name_ar' => ['required', 'string', 'max:150'],
            'name_en' => ['required', 'string', 'max:150'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'min_age_months' => ['nullable', 'integer', 'min:0', 'max:1200'],
            'max_age_months' => ['nullable', 'integer', 'min:0', 'max:1200', 'gte:min_age_months'],
            'status' => ['nullable', Rule::in(AcademicStage::statuses())],
        ];
    }

    public function attributes(): array
    {
        return [
            'institution_id' => __('Institution'),
            'code' => __('Stage code'),
            'name_ar' => __('Stage name (Arabic)'),
            'name_en' => __('Stage name (English)'),
            'description_ar' => __('Description (Arabic)'),
            'description_en' => __('Description (English)'),
            'min_age_months' => __('Minimum age in months'),
            'max_age_months' => __('Maximum age in months'),
            'status' => __('Status'),
        ];
    }
}
