<?php

namespace Modules\Accounting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Accounting\Services\LockDateService;

class StoreLockExceptionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'entity_id' => ['required', 'integer', 'exists:entities,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'lock_type' => ['required', 'string', 'in:' . implode(',', LockDateService::LOCK_TYPES)],
            'valid_from' => ['required', 'date'],
            'valid_until' => ['required', 'date', 'after:valid_from'],
            'reason' => ['required', 'string', 'max:2000'],
        ];
    }
}
