<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Academic\Models\AcademicWeeklyPlan;

class UpdateAcademicWeeklyPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'institution_id'    => ['required', 'integer', 'exists:nurseries,id'],
            'branch_id'         => ['nullable', 'integer'],
            'academic_stage_id' => ['required', 'integer', 'exists:academic_stages,id'],
            'academic_class_id' => ['required', 'integer', 'exists:academic_classes,id'],
            'title_ar'          => ['required', 'string', 'max:255'],
            'title_en'          => ['required', 'string', 'max:255'],
            'content_ar'        => ['nullable', 'string'],
            'content_en'        => ['nullable', 'string'],
            'start_date'        => ['nullable', 'date'],
            'end_date'          => ['nullable', 'date', 'after_or_equal:start_date'],
            'status'            => ['nullable', 'in:' . implode(',', AcademicWeeklyPlan::statuses())],
            'attachments'       => ['nullable', 'array'],
            'attachments.*'     => ['file', 'max:20480'],
        ];
    }

    public function attributes(): array
    {
        return [
            'institution_id'    => __('Educational Institution / Nursery'),
            'branch_id'         => __('Branch'),
            'academic_stage_id' => __('Academic Stage'),
            'academic_class_id' => __('Academic Class'),
            'title_ar'          => __('Plan Title (Arabic)'),
            'title_en'          => __('Plan Title (English)'),
            'content_ar'        => __('Plan Details / Content (Arabic)'),
            'content_en'        => __('Plan Details / Content (English)'),
            'start_date'        => __('Start Date'),
            'end_date'          => __('End Date'),
            'status'            => __('Status'),
            'attachments'       => __('Documents & Attachments'),
        ];
    }
}
