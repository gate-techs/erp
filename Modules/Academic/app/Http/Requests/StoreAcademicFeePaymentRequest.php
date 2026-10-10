<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicFeePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_fee_installment_id' => ['nullable', 'integer', 'exists:academic_fee_installments,id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_date' => ['required', 'date'],
            'invoice_number' => ['nullable', 'string', 'max:100'],
            'payment_number' => ['nullable', 'string', 'max:100'],
            'transaction_number' => ['nullable', 'string', 'max:150'],
            'reference_number' => ['nullable', 'string', 'max:150'],
            'tracking_number' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'in:paid,unpaid'],
        ];
    }
}
