<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\OnboardingStatus;

class Onboarding extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_onboardings';

    protected $fillable = [
        'onboarding_code',
        'candidate_id',
        'job_application_id',
        'job_offer_id',
        'employee_id',
        'department_id',
        'job_title_id',
        'buddy_employee_id',
        'start_date',
        'status',
        'progress_percentage',
        'notes',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'start_date'          => 'date',
        'completed_at'        => 'datetime',
        'progress_percentage' => 'integer',
    ];

    // ── Safe Enum Accessor & Mutator ──

    public function getStatusAttribute(?string $value): OnboardingStatus
    {
        return OnboardingStatus::tryFrom((string) $value) ?? OnboardingStatus::PENDING;
    }

    public function setStatusAttribute(mixed $value): void
    {
        $this->attributes['status'] = $value instanceof OnboardingStatus ? $value->value : $value;
    }

    // ── Relationships ──

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function jobOffer(): BelongsTo
    {
        return $this->belongsTo(JobOffer::class, 'job_offer_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class, 'job_title_id');
    }

    public function buddy(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'buddy_employee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(OnboardingTask::class, 'onboarding_id');
    }

    // ── Recalculate progress ──

    public function recalculateProgress(): int
    {
        $total = $this->tasks()->count();
        if ($total === 0) {
            $this->update(['progress_percentage' => 0]);
            return 0;
        }

        $completed = $this->tasks()->where('is_completed', true)->count();
        $pct = (int) round(($completed / $total) * 100);

        $data = ['progress_percentage' => $pct];
        if ($pct === 100 && $this->status !== OnboardingStatus::COMPLETED) {
            $data['status'] = OnboardingStatus::COMPLETED->value;
            $data['completed_at'] = now();
        } elseif ($pct > 0 && $this->status === OnboardingStatus::PENDING) {
            $data['status'] = OnboardingStatus::IN_PROGRESS->value;
        }

        $this->update($data);
        return $pct;
    }
}
