<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePersonalCalendarRequest extends FormRequest
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
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'event_date'     => ['required', 'date'],
            'start_time'     => ['nullable', 'date_format:H:i'],
            'end_time'       => ['nullable', 'date_format:H:i', 'after:start_time'],
            'is_all_day'     => ['nullable', 'boolean'],
            'status'         => ['nullable', 'in:planned,in_progress,completed,cancelled'],
            'color'          => ['nullable', 'string', 'max:20'],
            'institution_id' => ['nullable', 'exists:nurseries,id'],
        ];
    }

    //------------------------------
    public function attributes(): array
    {
        return [
            'title'          => __('Personal Event Title'),
            'description'    => __('Description'),
            'event_date'     => __('Date'),
            'start_time'     => __('Start Time'),
            'end_time'       => __('End Time'),
            'is_all_day'     => __('All Day'),
            'status'         => __('Status'),
            'color'          => __('Color'),
            'institution_id' => __('Nursery'),
        ];
    }
}
