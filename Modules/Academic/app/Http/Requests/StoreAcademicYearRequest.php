<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Academic\Models\AcademicYear;

class StoreAcademicYearRequest extends FormRequest
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
                'max:30',
                Rule::unique('academic_years', 'code')
                    ->where(fn ($query) => $query->where('institution_id', $this->integer('institution_id'))),
            ],
            'name_ar' => ['required', 'string', 'max:100'],
            'name_en' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', Rule::in(AcademicYear::statuses())],
            'is_current' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'institution_id' => __('Institution'),
            'code' => __('Academic year code'),
            'name_ar' => __('Academic year name (Arabic)'),
            'name_en' => __('Academic year name (English)'),
            'start_date' => __('Start date'),
            'end_date' => __('End date'),
            'status' => __('Status'),
            'is_current' => __('Current year'),
            'notes' => __('Notes'),
        ];
    }
}
