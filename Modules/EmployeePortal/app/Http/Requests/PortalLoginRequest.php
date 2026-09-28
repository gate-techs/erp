<?php

namespace Modules\EmployeePortal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PortalLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom Arabic validation error messages.
     */
    public function messages(): array
    {
        return [
            'email.required'    => 'يرجى إدخال البريد الإلكتروني',
            'email.email'       => 'يرجى إدخال بريد إلكتروني صالح',
            'password.required' => 'يرجى إدخال كلمة المرور',
        ];
    }
}
