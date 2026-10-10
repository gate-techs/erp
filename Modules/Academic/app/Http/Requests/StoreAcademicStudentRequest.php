<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicStudentRequest extends FormRequest
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
            // Student Core Info
            'name_ar'              => ['required', 'string', 'max:255'],
            'name_en'              => ['nullable', 'string', 'max:255'],
            'student_code'         => ['nullable', 'string', 'max:50', 'unique:academic_students,student_code'],
            'civil_id'             => ['nullable', 'string', 'max:30'],
            'institution_id'       => ['required', 'integer', 'exists:nurseries,id'],
            'branch_id'            => ['nullable', 'integer', 'exists:nursery_branches,id'],
            'academic_year_id'     => ['nullable', 'integer', 'exists:academic_years,id'],
            'academic_stage_id'    => ['nullable', 'integer', 'exists:academic_stages,id'],
            'academic_class_id'    => ['nullable', 'integer', 'exists:academic_classes,id'],
            'nationality_id'       => ['nullable', 'integer', 'exists:general_settings,id'],
            'academic_guardian_id' => ['nullable', 'integer', 'exists:academic_guardians,id'],
            'primary_guardian_type'=> ['nullable', 'string', 'in:father,mother,existing'],
            'birth_date'           => ['nullable', 'date'],
            'gender'               => ['required', 'integer', 'in:1,2'],
            'photo'                => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'status'               => ['nullable', 'string', 'in:active,pending,inactive,graduated,withdrawn'],

            // Father
            'father.name'           => ['nullable', 'string', 'max:255'],
            'father.civil_id'       => ['nullable', 'string', 'max:30'],
            'father.nationality'    => ['nullable', 'string', 'max:100'],
            'father.occupation'     => ['nullable', 'string', 'max:100'],
            'father.workplace'      => ['nullable', 'string', 'max:150'],
            'father.company_branch' => ['nullable', 'string', 'max:150'],
            'father.marital_status' => ['nullable', 'integer'],
            'father.phone'          => ['nullable', 'string', 'max:30'],
            'father.email'          => ['nullable', 'email', 'max:100'],

            // Mother
            'mother.name'           => ['nullable', 'string', 'max:255'],
            'mother.civil_id'       => ['nullable', 'string', 'max:30'],
            'mother.nationality'    => ['nullable', 'string', 'max:100'],
            'mother.occupation'     => ['nullable', 'string', 'max:100'],
            'mother.workplace'      => ['nullable', 'string', 'max:150'],
            'mother.company_branch' => ['nullable', 'string', 'max:150'],
            'mother.marital_status' => ['nullable', 'integer'],
            'mother.phone'          => ['nullable', 'string', 'max:30'],
            'mother.email'          => ['nullable', 'email', 'max:100'],

            // Terms & Consents
            'terms_accepted'               => ['nullable', 'boolean'],
            'financial_policy_accepted'    => ['nullable', 'boolean'],
            'pickup_policy_accepted'       => ['nullable', 'boolean'],
            'medical_declaration_accepted' => ['nullable', 'boolean'],
            'photo_consent'                => ['nullable', 'boolean'],
            'photo_consent_notes'          => ['nullable', 'string'],
            'guardian_notes'               => ['nullable', 'string'],
            'admin_notes'                  => ['nullable', 'string'],

            // Nested arrays
            'siblings'                  => ['nullable', 'array'],
            'diseases'                  => ['nullable', 'array'],
            'medical_responses'         => ['nullable', 'array'],
            'emergency_contacts'        => ['nullable', 'array'],
            'authorized_pickups'        => ['nullable', 'array'],
            'information_responses'     => ['nullable', 'array'],
        ];
    }

    //------------------------------------------
    public function messages(): array
    {
        return [
            'name_ar.required'        => __('Student Arabic name is required.'),
            'institution_id.required' => __('Nursery/institution is required.'),
            'gender.required'         => __('Gender is required.'),
            'photo.image'             => __('Photo must be an image.'),
            'photo.max'               => __('Photo size must not exceed 2MB.'),
        ];
    }
}
