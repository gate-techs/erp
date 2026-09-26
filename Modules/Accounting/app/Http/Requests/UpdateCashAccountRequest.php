<?php

namespace Modules\Accounting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Accounting\Enums\CashAccountType;

class UpdateCashAccountRequest extends FormRequest
{
    //--------------------------------------------------
    public function authorize(): bool
    {
        return true;
    }

    //--------------------------------------------------
    public function rules(): array
    {
        return [
            'entity_id' => ['sometimes', 'required', 'integer', 'exists:entities,id'],
            'account_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'journal_id' => ['nullable', 'integer', 'exists:journals,id'],
            'type' => ['sometimes', 'required', 'string', Rule::in(CashAccountType::values())],
            'code' => ['sometimes', 'required', 'string', 'max:20'],
            'name_ar' => ['sometimes', 'required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:100'],
            'iban' => ['nullable', 'string', 'max:50'],
            'swift_code' => ['nullable', 'string', 'max:30'],
            'currency' => ['nullable', 'string', 'max:10'],
            'opening_balance' => ['nullable', 'numeric'],
            'responsible_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'description_en' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
