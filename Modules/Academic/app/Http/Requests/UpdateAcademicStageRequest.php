<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Validation\Rule;
use Modules\Academic\Models\AcademicStage;

class UpdateAcademicStageRequest extends StoreAcademicStageRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $stageId = (int) $this->route('id');
        $institutionId = $this->input('institution_id');

        if (!$institutionId && $stageId) {
            $institutionId = AcademicStage::query()
                ->whereKey($stageId)
                ->value('institution_id');
        }

        $rules['institution_id'] = ['sometimes', 'integer', 'min:1'];
        $rules['code'] = [
            'sometimes',
            'string',
            'max:50',
            Rule::unique('academic_stages', 'code')
                ->where(fn ($query) => $query->where('institution_id', $institutionId))
                ->ignore($stageId),
        ];
        $rules['name_ar'] = ['sometimes', 'string', 'max:150'];
        $rules['name_en'] = ['sometimes', 'string', 'max:150'];
        $rules['description_ar'] = ['nullable', 'string'];
        $rules['description_en'] = ['nullable', 'string'];
        $rules['min_age_months'] = ['nullable', 'integer', 'min:0', 'max:1200'];
        $rules['max_age_months'] = ['nullable', 'integer', 'min:0', 'max:1200'];

        return $rules;
    }
}
