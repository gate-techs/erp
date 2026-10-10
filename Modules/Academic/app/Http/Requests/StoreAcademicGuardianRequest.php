<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicGuardianRequest extends FormRequest
{
    //------------------------------------------
    public function authorize(): bool
    {
        return true;
    }

    //------------------------------------------
    public function rules(): array
    {
        $isExistingUser = $this->boolean('is_employee') || ! empty($this->input('existing_user_id'));

        return [
            'name_ar'          => [$isExistingUser ? 'nullable' : 'required', 'string', 'max:255'],
            'name_en'          => ['nullable', 'string', 'max:255'],
            'civil_id'         => ['nullable', 'string', 'max:30'],
            'institution_id'   => ['required', 'integer', 'exists:nurseries,id'],
            'branch_id'        => ['nullable', 'integer', 'exists:nursery_branches,id'],
            'phone_code'       => ['nullable', 'string', 'max:10'],
            'phone'            => ['required', 'string', 'max:30'],
            'email'            => ['nullable', 'email', 'max:100'],
            'password'         => [$isExistingUser ? 'nullable' : 'required', 'nullable', 'string', 'min:6'],
            'photo'            => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'status'           => ['required', 'string', 'in:active,inactive'],
            'is_employee'      => ['nullable', 'boolean'],
            'existing_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }

    //------------------------------------------
    public function messages(): array
    {
        return [
            'name_ar.required'        => __('Arabic name is required.'),
            'institution_id.required' => __('Nursery/institution is required.'),
            'phone.required'          => __('Phone number is required.'),
            'password.required'       => __('Password is required for new accounts.'),
            'password.min'            => __('Password must be at least 6 characters.'),
            'status.required'         => __('Status is required.'),
            'photo.image'             => __('Photo must be an image.'),
            'photo.max'               => __('Photo size must not exceed 2MB.'),
        ];
    }
}
