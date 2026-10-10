<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherGalleryRequest extends FormRequest
{
    //------------------------------------------
    public function authorize(): bool
    {
        return true;
    }

    //------------------------------------------
    public function rules(): array
    {
        return [
            'teacher_id'    => ['nullable', 'integer', 'exists:employees,id'],
            'class_room_id' => ['required', 'integer', 'exists:academic_classes,id'],
            'student_id'    => ['nullable', 'integer', 'exists:academic_students,id'],
            'student_ids'   => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'exists:academic_students,id'],
            'title'         => ['nullable', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'date'          => ['nullable', 'date'],
            'images'        => ['nullable', 'array'],
            'images.*'      => ['file', 'mimes:jpeg,png,jpg,gif,svg,webp,mp4,mov,avi,wmv,webm', 'max:51200'],
        ];
    }

    //------------------------------------------
    public function attributes(): array
    {
        return [
            'teacher_id'    => __('Teacher'),
            'class_room_id' => __('Classroom'),
            'student_id'    => __('Student'),
            'student_ids'   => __('Selected Students'),
            'title'         => __('Gallery Title'),
            'description'   => __('Description'),
            'date'          => __('Date'),
            'images'        => __('Media Files'),
            'images.*'      => __('Media File'),
        ];
    }
}
