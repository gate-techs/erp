<?php

namespace Modules\Accounting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Accounting\Enums\CostCenterStatus;
use Modules\Accounting\Enums\CostCenterType;

class UpdateCostCenterRequest extends FormRequest
{
    //===============================
    public function authorize(): bool
    {
        return true;
    }

    //===============================
    public function rules(): array
    {
        $costCenterId = (int) $this->route('cost_center') ?: (int) $this->route('id') ?: (int) $this->input('id');

        return [
            'entity_id'      => ['nullable', 'integer', 'exists:entities,id'],
            'code'           => ['required', 'string', 'max:50'],
            'name_ar'        => ['required', 'string', 'max:255'],
            'name_en'        => ['nullable', 'string', 'max:255'],
            'type'           => ['required', 'string', Rule::in(CostCenterType::values())],
            'status'         => ['nullable', 'string', Rule::in(CostCenterStatus::values())],
            'parent_id'      => ['nullable', 'integer', 'exists:accounting_cost_centers,id'],
            'branch_id'      => ['nullable', 'integer', 'exists:nursery_branches,id'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'description_en' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
