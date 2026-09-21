<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\JobOfferStatus;

class JobOffer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_job_offers';

    protected $fillable = [
        'offer_code',
        'job_application_id',
        'candidate_id',
        'job_opening_id',
        'department_id',
        'job_title_id',
        'employment_type',
        'basic_salary',
        'housing_allowance',
        'transportation_allowance',
        'other_allowances',
        'total_salary',
        'currency',
        'offer_date',
        'expiry_date',
        'joining_date',
        'probation_months',
        'contract_duration_months',
        'status',
        'terms_and_conditions',
        'notes',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'sent_at',
        'responded_at',
        'created_by',
    ];

    protected $casts = [
        'offer_date'               => 'date',
        'expiry_date'              => 'date',
        'joining_date'             => 'date',
        'approved_at'              => 'datetime',
        'sent_at'                  => 'datetime',
        'responded_at'             => 'datetime',
        'basic_salary'             => 'float',
        'housing_allowance'        => 'float',
        'transportation_allowance' => 'float',
        'other_allowances'         => 'float',
        'total_salary'             => 'float',
        'probation_months'         => 'integer',
        'contract_duration_months' => 'integer',
    ];

    // ── Safe Enum Accessor & Mutator ──

    public function getStatusAttribute(?string $value): JobOfferStatus
    {
        return JobOfferStatus::tryFrom((string) $value) ?? JobOfferStatus::DRAFT;
    }

    public function setStatusAttribute(mixed $value): void
    {
        $this->attributes['status'] = $value instanceof JobOfferStatus ? $value->value : $value;
    }

    // ── Relationships ──

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class, 'job_application_id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class, 'job_opening_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class, 'job_title_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function onboarding(): HasOne
    {
        return $this->hasOne(Onboarding::class, 'job_offer_id');
    }

    // ── Helper Accessors ──

    public function calculateTotalSalary(): float
    {
        return (float) ($this->basic_salary + $this->housing_allowance + $this->transportation_allowance + $this->other_allowances);
    }
}
