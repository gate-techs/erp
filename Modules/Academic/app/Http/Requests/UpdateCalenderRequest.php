<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCalenderRequest extends FormRequest
{
    //------------------------------
    public function authorize(): bool
    {
        return true;
    }

    //------------------------------
    public function rules(): array
    {
        return [
            'title_ar'       => ['required', 'string', 'max:255'],
            'title_en'       => ['required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'start_date'     => ['required', 'date'],
            'end_date'       => ['nullable', 'date', 'after_or_equal:start_date'],
            'institution_id' => ['required', 'exists:nurseries,id'],
            'status'         => ['nullable', 'in:active,inactive'],
        ];
    }

    //------------------------------
    public function attributes(): array
    {
        return [
            'title_ar'       => __('Event Name (Arabic)'),
            'title_en'       => __('Event Name (English)'),
            'description_ar' => __('Description (Arabic)'),
            'description_en' => __('Description (English)'),
            'start_date'     => __('Start Date'),
            'end_date'       => __('End Date'),
            'institution_id' => __('Nursery'),
            'status'         => __('Status'),
        ];
    }
}
