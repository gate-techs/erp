<?php

namespace Modules\Academic\Http\Requests;

class UpdateAcademicClassRequest extends StoreAcademicClassRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        foreach (['institution_id', 'branch_id', 'academic_year_id', 'academic_stage_id', 'name_ar', 'name_en'] as $field) {
            $rules[$field][0] = 'sometimes';
        }

        return $rules;
    }
}
