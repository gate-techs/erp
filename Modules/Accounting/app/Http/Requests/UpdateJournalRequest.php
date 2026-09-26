<?php

namespace Modules\Accounting\Http\Requests;

class UpdateJournalRequest extends StoreJournalRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['entity_id'] = ['nullable', 'integer', 'exists:entities,id'];

        return $rules;
    }
}
