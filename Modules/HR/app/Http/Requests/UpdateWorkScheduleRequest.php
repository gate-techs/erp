<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HR\Enums\WorkScheduleStatus;
use Modules\HR\Enums\WorkScheduleType;

class UpdateWorkScheduleRequest extends FormRequest
{
    //======================
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    //======================
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('work_schedule') ?? $this->id;

        return [
            'code'           => ['nullable', 'string', 'max:50', Rule::unique('work_schedules', 'code')->ignore($id)],
            'name_ar'        => ['required', 'string', 'max:255'],
            'name_en'        => ['required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'type'           => ['required', Rule::in(WorkScheduleType::values())],
            'branch_id'      => ['nullable', 'integer', 'exists:nursery_branches,id'],
            'department_id'  => ['nullable', 'integer', 'exists:departments,id'],
            'is_default'     => ['nullable', 'boolean'],
            'color'          => ['nullable', 'string', 'max:20'],
            'status'         => ['required', Rule::in(WorkScheduleStatus::values())],
            'days'           => ['nullable', 'array'],
            'days.*.is_off_day'      => ['nullable', 'boolean'],
            'days.*.shift_id'        => ['nullable', 'integer', 'exists:shifts,id'],
            'days.*.second_shift_id' => ['nullable', 'integer', 'exists:shifts,id'],
        ];
    }

    //======================
    /**
     * Custom attributes labels for validation messages.
     */
    public function attributes(): array
    {
        return [
            'code'           => __('Schedule Code'),
            'name_ar'        => __('Schedule Name [Arabic]'),
            'name_en'        => __('Schedule Name [English]'),
            'description_ar' => __('Description [Arabic]'),
            'description_en' => __('Description [English]'),
            'type'           => __('Schedule Type'),
            'branch_id'      => __('Branch'),
            'department_id'  => __('Department'),
            'is_default'     => __('Default Schedule'),
            'color'          => __('Color'),
            'status'         => __('Status'),
            'days'           => __('Schedule Days'),
        ];
    }
}
