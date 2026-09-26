<?php

namespace Modules\Accounting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Accounting\Enums\JournalEntryType;

class StoreJournalEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'entity_id' => ['required', 'integer', 'exists:entities,id'],
            'journal_id' => ['required', 'integer', 'exists:journals,id'],
            'entry_date' => ['required', 'date'],
            'accounting_date' => ['nullable', 'date'],
            'reference' => ['nullable', 'string', 'max:100'],
            'entry_type' => ['nullable', 'string', Rule::in(JournalEntryType::values())],
            'currency' => ['nullable', 'string', 'max:10'],
            'exchange_rate' => ['nullable', 'numeric', 'min:0.000001'],
            'narration' => ['required', 'string', 'max:2000'],
            'post_immediately' => ['nullable', 'boolean'],

            // Lines array validation
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_id' => ['required', 'integer', 'exists:accounts,id'],
            'lines.*.cost_center_id' => ['nullable', 'integer', 'exists:accounting_cost_centers,id'],
            'lines.*.partner_id' => ['nullable', 'integer'],
            'lines.*.partner_type' => ['nullable', 'string', 'max:100'],
            'lines.*.partner_name' => ['nullable', 'string', 'max:255'],
            'lines.*.description' => ['nullable', 'string', 'max:500'],
            'lines.*.debit' => ['required_without:lines.*.credit', 'numeric', 'min:0'],
            'lines.*.credit' => ['required_without:lines.*.debit', 'numeric', 'min:0'],
            'lines.*.currency' => ['nullable', 'string', 'max:10'],
            'lines.*.amount_currency' => ['nullable', 'numeric'],
        ];
    }
}
