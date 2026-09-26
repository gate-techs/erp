<?php

namespace Modules\Accounting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Accounting\Enums\JournalType;

class StoreJournalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'entity_id' => ['required', 'integer', 'exists:entities,id'],
            'code' => ['required', 'string', 'max:5'],
            'type' => ['required', 'string', Rule::in(JournalType::values())],
            'currency' => ['nullable', 'string', 'max:10'],
            'sequence_prefix' => ['nullable', 'string', 'max:50'],
            'sequence_number_next' => ['required', 'integer', 'min:1'],
            'refund_sequence' => ['nullable', 'boolean'],
            'payment_sequence' => ['nullable', 'boolean'],
            'default_account_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'suspense_account_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'profit_account_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'loss_account_id' => ['nullable', 'integer', 'exists:accounts,id'],
            'allow_manual_entries' => ['nullable', 'boolean'],
            'reconcile' => ['nullable', 'boolean'],
            'restrict_mode_hash_table' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'description_en' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
