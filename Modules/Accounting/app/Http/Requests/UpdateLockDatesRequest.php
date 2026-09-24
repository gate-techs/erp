<?php

namespace Modules\Accounting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLockDatesRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'lock_all_date' => ['nullable', 'date'],
            'tax_lock_date' => ['nullable', 'date'],
            'fiscal_year_lock_date' => ['nullable', 'date'],
            'hard_lock_date' => ['nullable', 'date'],
        ];
    }
}
