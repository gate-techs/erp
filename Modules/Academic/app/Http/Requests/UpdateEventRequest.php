<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name_ar'           => ['required', 'string', 'max:255'],
            'name_en'           => ['required', 'string', 'max:255'],
            'description_ar'    => ['nullable', 'string'],
            'description_en'    => ['nullable', 'string'],
            'institution_id'    => ['required', 'integer', 'exists:nurseries,id'],
            'branch_id'         => ['required', 'integer', 'exists:nursery_branches,id'],
            'academic_class_id' => ['nullable', 'integer', 'exists:academic_classes,id'],
            'event_type_id'     => ['required', 'integer', 'exists:general_settings,id'],
            'start_date'        => ['required', 'date'],
            'end_date'          => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time'        => ['nullable', 'string'],
            'end_time'          => ['nullable', 'string'],
            'price'             => ['nullable', 'numeric', 'min:0'],
            'status'            => ['nullable', 'string', 'in:active,inactive,upcoming,completed'],
            'image'             => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'name_ar'           => __('Event Name (Arabic)'),
            'name_en'           => __('Event Name (English)'),
            'description_ar'    => __('Event Details (Arabic)'),
            'description_en'    => __('Event Details (English)'),
            'institution_id'    => __('Nursery'),
            'branch_id'         => __('Branch'),
            'academic_class_id' => __('Academic Class'),
            'event_type_id'     => __('Event Type'),
            'start_date'        => __('Start Date'),
            'end_date'          => __('End Date'),
            'price'             => __('Price'),
            'status'            => __('Status'),
            'image'             => __('Image'),
        ];
    }
}
