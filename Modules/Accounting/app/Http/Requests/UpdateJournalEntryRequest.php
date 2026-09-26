<?php

namespace Modules\Accounting\Http\Requests;

class UpdateJournalEntryRequest extends StoreJournalEntryRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        return $rules;
    }
}
