<?php

namespace Modules\Tasks\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Tasks\Enums\TaskPriority;
use Modules\Tasks\Enums\TaskStatus;

class UpdateTaskRequest extends FormRequest
{
    //-----------------------------------------
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    //-----------------------------------------
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title_ar'           => ['required', 'string', 'max:255'],
            'title_en'           => ['required', 'string', 'max:255'],
            'body_ar'            => ['nullable', 'string'],
            'body_en'            => ['nullable', 'string'],
            'task_group_id'      => ['nullable', 'exists:task_groups,id'],
            'employee_id'        => ['nullable', 'exists:employees,id'],
            'priority'           => ['required', Rule::in(TaskPriority::values())],
            'task_status'        => ['required', Rule::in(TaskStatus::values())],
            'start_date'         => ['nullable', 'date'],
            'completion_date'    => ['nullable', 'date', 'after_or_equal:start_date'],
            'attachments'        => ['nullable', 'array'],
            'attachments.*'      => ['file', 'mimes:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx', 'max:10240'],
            'delete_attachments' => ['nullable', 'array'],
            'delete_attachments.*' => ['integer', 'exists:task_attachments,id'],
        ];
    }

    //-----------------------------------------
    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title_ar'           => __('Title in Arabic'),
            'title_en'           => __('Title in English'),
            'body_ar'            => __('Details / Body in Arabic'),
            'body_en'            => __('Details / Body in English'),
            'task_group_id'      => __('Task Group'),
            'employee_id'        => __('Assigned Employee'),
            'priority'           => __('Priority Level'),
            'task_status'        => __('Task Status'),
            'start_date'         => __('Start Date'),
            'completion_date'    => __('Completion Date'),
            'attachments'        => __('Attachments'),
            'attachments.*'      => __('Attachment File'),
            'delete_attachments' => __('Deleted Attachments'),
        ];
    }
}
