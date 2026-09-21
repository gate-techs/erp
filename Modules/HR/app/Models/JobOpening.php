<?php

namespace Modules\HR\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\JobOpeningStatus;
use Modules\HR\Enums\PostingType;
use Modules\HR\Enums\WorkplaceType;
use Modules\Nursery\Models\NurseryBranch;

class JobOpening extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    protected $table = 'hr_job_openings';

    public array $translatedAttributes = [
        'title',
        'job_summary',
        'responsibilities',
        'requirements',
        'benefits',
        'education_level',
    ];

    public string $translationModel = JobOpeningTranslation::class;

    public string $translationForeignKey = 'job_opening_id';

    protected $fillable = [
        'opening_code',
        'job_requisition_id',
        'title',
        'job_title_id',
        'department_id',
        'branch_id',
        'employment_type',
        'workplace_type',
        'posting_type',
        'open_positions',
        'filled_positions',
        'min_experience_years',
        'max_experience_years',
        'education_level',
        'min_salary',
        'max_salary',
        'currency',
        'hide_salary',
        'publish_date',
        'closing_date',
        'job_summary',
        'responsibilities',
        'requirements',
        'benefits',
        'status',
        'views_count',
        'applications_count',
        'created_by',
        'published_by',
        'published_at',
        'closed_at',
    ];

    protected $casts = [
        'employment_type'      => EmploymentType::class,
        'workplace_type'       => WorkplaceType::class,
        'posting_type'         => PostingType::class,
        'status'               => JobOpeningStatus::class,
        'open_positions'       => 'integer',
        'filled_positions'     => 'integer',
        'min_experience_years' => 'integer',
        'max_experience_years' => 'integer',
        'min_salary'           => 'decimal:3',
        'max_salary'           => 'decimal:3',
        'hide_salary'          => 'boolean',
        'publish_date'         => 'date',
        'closing_date'         => 'date',
        'published_at'         => 'datetime',
        'closed_at'            => 'datetime',
        'views_count'          => 'integer',
        'applications_count'   => 'integer',
    ];

    /**
     * Linked job requisition.
     */
    public function requisition(): BelongsTo
    {
        return $this->belongsTo(JobRequisition::class, 'job_requisition_id');
    }

    /**
     * Job title.
     */
    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class, 'job_title_id');
    }

    /**
     * Department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Branch.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
    }

    /**
     * Creator.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Publisher.
     */
    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    /**
     * Remaining positions to be filled.
     */
    public function getRemainingPositionsAttribute(): int
    {
        return max(0, $this->open_positions - $this->filled_positions);
    }

    /**
     * Check if opening is published and active.
     */
    public function isActive(): bool
    {
        return $this->status === JobOpeningStatus::PUBLISHED
            && ($this->closing_date === null || $this->closing_date->isFuture() || $this->closing_date->isToday());
    }

    /**
     * Scope for active openings.
     */
    public function scopeActive($query)
    {
        return $query->where('status', JobOpeningStatus::PUBLISHED->value)
            ->where(function ($q) {
                $q->whereNull('closing_date')
                  ->orWhere('closing_date', '>=', now()->toDateString());
            });
    }

    /**
     * Applications submitted for this job opening.
     */
    public function applications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(JobApplication::class, 'job_opening_id');
    }
}
