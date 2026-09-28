<?php

namespace Modules\EmployeePortal\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PortalPunchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'punch_type' => ['nullable', 'in:check_in,check_out,auto'],
            'latitude'   => ['nullable', 'numeric'],
            'longitude'  => ['nullable', 'numeric'],
            'notes'      => ['nullable', 'string', 'max:255'],
        ];
    }
}
