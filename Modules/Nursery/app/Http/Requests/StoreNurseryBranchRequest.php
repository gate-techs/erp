<?php

namespace Modules\Nursery\Http\Requests;

use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Nursery\Enums\DayOfWeek;
use Modules\Nursery\Enums\NurseryStatus;

class StoreNurseryBranchRequest extends FormRequest
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
            // Parent Nursery
            'nursery_id' => ['required', 'exists:nurseries,id'],

            // Translatable fields
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['required', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'address_ar' => ['nullable', 'string', 'max:500'],
            'address_en' => ['nullable', 'string', 'max:500'],

            // Location & Employee
            'government_id' => ['nullable', 'exists:general_settings,id'],
            'city_id' => ['nullable', 'exists:general_settings,id'],
            'employee_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->where('user_type', UserType::EMPLOYEE->value);
                }),
            ],

            // Coordinates & Contacts
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', Rule::in(NurseryStatus::values())],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],

            // Weekly schedule
            'work_days' => ['nullable', 'array'],
            'work_days.*.day' => ['nullable', Rule::in(DayOfWeek::values())],
            'work_days.*.day_name' => ['nullable', 'string'],
            'work_days.*.from_time' => ['nullable', 'date_format:H:i'],
            'work_days.*.to_time' => ['nullable', 'date_format:H:i'],
            'work_days.*.is_off' => ['nullable', 'boolean'],
        ];
    }

    //======================
    /**
     * Custom attribute names for validation errors.
     */
    public function attributes(): array
    {
        return [
            'nursery_id' => __('Nursery'),
            'name_ar' => __('Branch Name [Arabic]'),
            'name_en' => __('Branch Name [English]'),
            'description_ar' => __('Branch Details [Arabic]'),
            'description_en' => __('Branch Details [English]'),
            'address_ar' => __('Branch Address [Arabic]'),
            'address_en' => __('Branch Address [English]'),
            'government_id' => __('Government'),
            'city_id' => __('City / Area'),
            'employee_id' => __('Responsible Employee'),
            'image' => __('Branch Image / Logo'),
            'whatsapp' => __('WhatsApp Number'),
            'email' => __('Email Address'),
            'status' => __('Status'),
        ];
    }
}
