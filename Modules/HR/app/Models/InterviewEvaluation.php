<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HR\Enums\EvaluationRecommendation;

class InterviewEvaluation extends Model
{
    use HasFactory;

    protected $table = 'hr_interview_evaluations';

    protected $fillable = [
        'interview_id',
        'interviewer_id',
        'technical_score',
        'communication_score',
        'problem_solving_score',
        'cultural_fit_score',
        'experience_score',
        'average_score',
        'recommendation',
        'strengths',
        'weaknesses',
        'notes',
        'submitted_at',
    ];

    protected $casts = [
        'technical_score'       => 'integer',
        'communication_score'   => 'integer',
        'problem_solving_score' => 'integer',
        'cultural_fit_score'    => 'integer',
        'experience_score'      => 'integer',
        'average_score'         => 'float',
        'recommendation'        => EvaluationRecommendation::class,
        'submitted_at'          => 'datetime',
    ];

    /**
     * Relationship: Interview
     */
    public function interview(): BelongsTo
    {
        return $this->belongsTo(Interview::class, 'interview_id');
    }

    /**
     * Relationship: Interviewer (Employee)
     */
    public function interviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'interviewer_id');
    }

    /**
     * Calculate average score automatically from available scores
     */
    public function calculateAverage(): float
    {
        $scores = array_filter([
            $this->technical_score,
            $this->communication_score,
            $this->problem_solving_score,
            $this->cultural_fit_score,
            $this->experience_score,
        ], fn ($val) => !is_null($val) && $val > 0);

        if (empty($scores)) {
            return 0.00;
        }

        return round(array_sum($scores) / count($scores), 2);
    }
}
