<?php

namespace Modules\Nursery\Http\Requests;

use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Nursery\Enums\DayOfWeek;
use Modules\Nursery\Enums\NurseryStatus;

class StoreNurseryRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Translatable fields
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],

            // Relationships
            'government_id' => ['nullable', 'exists:general_settings,id'],
            'employee_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('user_type', UserType::EMPLOYEE->value);
                }),
            ],

            // Geolocation & Contacts
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', Rule::in(NurseryStatus::values())],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],

            // Weekly schedule
            'work_days' => ['nullable', 'array'],
            'work_days.*.day' => ['required_with:work_days', Rule::in(DayOfWeek::values())],
            'work_days.*.from_time' => ['nullable', 'date_format:H:i'],
            'work_days.*.to_time' => ['nullable', 'date_format:H:i'],
            'work_days.*.is_off' => ['nullable', 'boolean'],
        ];
    }

    //======================
    /**
     * Custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name_ar' => __('Nursery Name (Arabic)'),
            'name_en' => __('Nursery Name (English)'),
            'description_ar' => __('Details (Arabic)'),
            'description_en' => __('Details (English)'),
            'government_id' => __('Government'),
            'employee_id' => __('Responsible Employee'),
            'lat' => __('Latitude'),
            'lng' => __('Longitude'),
            'whatsapp' => __('WhatsApp Number'),
            'email' => __('Email Address'),
            'status' => __('Status'),
            'image' => __('Nursery Logo / Image'),
        ];
    }
}
