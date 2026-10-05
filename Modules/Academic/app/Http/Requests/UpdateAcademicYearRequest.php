<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Academic\Models\AcademicYear;

class UpdateAcademicYearRequest extends StoreAcademicYearRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $academicYearId = (int) $this->route('id');
        $institutionId = $this->input('institution_id');

        if (!$institutionId && $academicYearId) {
            $institutionId = AcademicYear::query()
                ->whereKey($academicYearId)
                ->value('institution_id');
        }

        $rules['institution_id'] = ['sometimes', 'integer', 'min:1'];
        $rules['code'] = [
            'sometimes',
            'string',
            'max:30',
            Rule::unique('academic_years', 'code')
                ->where(fn ($query) => $query->where('institution_id', $institutionId))
                ->ignore($academicYearId),
        ];
        $rules['name_ar'] = ['sometimes', 'string', 'max:100'];
        $rules['name_en'] = ['sometimes', 'string', 'max:100'];
        $rules['start_date'] = ['sometimes', 'date'];
        $rules['end_date'] = ['sometimes', 'date', 'after_or_equal:start_date'];

        return $rules;
    }
}
