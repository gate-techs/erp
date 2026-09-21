<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\EvaluationRecommendation;
use Modules\HR\Enums\InterviewMode;
use Modules\HR\Enums\InterviewStatus;
use Modules\HR\Enums\InterviewType;

class Interview extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_interviews';

    protected $fillable = [
        'job_application_id',
        'job_opening_id',
        'candidate_id',
        'interview_round',
        'title',
        'type',
        'mode',
        'status',
        'scheduled_at',
        'duration_minutes',
        'location',
        'meeting_link',
        'notes',
        'overall_score',
        'final_recommendation',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at'     => 'datetime',
        'duration_minutes' => 'integer',
        'overall_score'    => 'float',
    ];

    // ── Safe Enum Accessors (use tryFrom to avoid ValueError on stale DB values) ──

    public function getTypeAttribute(?string $value): InterviewType
    {
        return InterviewType::tryFrom((string) $value) ?? InterviewType::HR;
    }

    public function getModeAttribute(?string $value): InterviewMode
    {
        return InterviewMode::tryFrom((string) $value) ?? InterviewMode::IN_PERSON;
    }

    public function getStatusAttribute(?string $value): InterviewStatus
    {
        return InterviewStatus::tryFrom((string) $value) ?? InterviewStatus::SCHEDULED;
    }

    public function getFinalRecommendationAttribute(?string $value): ?EvaluationRecommendation
    {
        return $value ? EvaluationRecommendation::tryFrom($value) : null;
    }

    // ── Mutators: store the raw string value ──

    public function setTypeAttribute(mixed $value): void
    {
        $this->attributes['type'] = $value instanceof InterviewType ? $value->value : $value;
    }

    public function setModeAttribute(mixed $value): void
    {
        $this->attributes['mode'] = $value instanceof InterviewMode ? $value->value : $value;
    }

    public function setStatusAttribute(mixed $value): void
    {
        $this->attributes['status'] = $value instanceof InterviewStatus ? $value->value : $value;
    }

    public function setFinalRecommendationAttribute(mixed $value): void
    {
        $this->attributes['final_recommendation'] = $value instanceof EvaluationRecommendation ? $value->value : $value;
    }

    /**
     * Relationship: Job Application
     */
    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    /**
     * Relationship: Job Opening
     */
    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class, 'job_opening_id');
    }

    /**
     * Relationship: Candidate
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    /**
     * Relationship: Creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: Interviewers (Employees)
     */
    public function interviewers(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'hr_interview_interviewers', 'interview_id', 'employee_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    /**
     * Relationship: Evaluations
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(InterviewEvaluation::class, 'interview_id');
    }

    protected $appends = ['interview_code'];

    /**
     * Get computed interview code (e.g. INT-2026-0001)
     */
    public function getInterviewCodeAttribute(): string
    {
        $year = $this->created_at ? $this->created_at->format('Y') : date('Y');
        return 'INT-' . $year . '-' . str_pad((string) $this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope: Upcoming interviews
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', InterviewStatus::SCHEDULED)
                     ->where('scheduled_at', '>=', now())
                     ->orderBy('scheduled_at', 'asc');
    }
}
