<?php

namespace Modules\Academic\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAcademicStudentFeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'academic_student_id' => ['required', 'integer', 'exists:academic_students,id'],
            'academic_fee_id' => ['required', 'integer', 'exists:academic_fees,id'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'installments_count' => ['required', 'integer', 'min:1', 'max:120'],
            'start_date' => ['required', 'date'],
        ];
    }
}
