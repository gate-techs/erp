<?php

namespace Modules\HR\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\HR\Enums\EvaluationRecommendation;

class StoreEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'interviewer_id'        => 'required|exists:employees,id',
            'technical_score'       => 'required|integer|min:1|max:5',
            'communication_score'   => 'required|integer|min:1|max:5',
            'problem_solving_score' => 'required|integer|min:1|max:5',
            'cultural_fit_score'    => 'required|integer|min:1|max:5',
            'experience_score'      => 'required|integer|min:1|max:5',
            'recommendation'        => ['required', new Enum(EvaluationRecommendation::class)],
            'strengths'             => 'nullable|string',
            'weaknesses'            => 'nullable|string',
            'notes'                 => 'nullable|string',
        ];
    }
}
