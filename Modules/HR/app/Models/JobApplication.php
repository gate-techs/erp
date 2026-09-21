<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\ApplicationStage;
use Modules\HR\Enums\ApplicationStatus;
use Modules\HR\Enums\RejectionReason;

class JobApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_job_applications';

    protected $fillable = [
        'application_code',
        'candidate_id',
        'job_opening_id',
        'stage',
        'status',
        'rating',
        'expected_salary',
        'currency',
        'notice_period_days',
        'source',
        'cover_letter',
        'rejection_reason',
        'rejection_notes',
        'rejected_at',
        'hired_at',
        'created_by',
    ];

    protected $casts = [
        'stage'            => ApplicationStage::class,
        'status'           => ApplicationStatus::class,
        'rejection_reason' => RejectionReason::class,
        'rating'           => 'integer',
        'expected_salary'  => 'float',
        'rejected_at'      => 'datetime',
        'hired_at'         => 'datetime',
    ];

    /**
     * Relationship: Candidate
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    /**
     * Relationship: Job Opening
     */
    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class, 'job_opening_id');
    }

    /**
     * Relationship: User who created or submitted application
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relationship: Interviews scheduled for this application
     */
    public function interviews(): HasMany
    {
        return $this->hasMany(Interview::class, 'job_application_id')->orderBy('scheduled_at', 'asc');
    }
}
