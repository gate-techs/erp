<?php

namespace Modules\HR\Models;

use Modules\Nursery\Models\NurseryBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\PlanItemPriority;
use Modules\HR\Enums\PlanQuarter;

class WorkforcePlanItem extends Model
{
    use HasFactory;

    protected $table = 'hr_workforce_plan_items';

    protected $fillable = [
        'workforce_plan_id',
        'department_id',
        'branch_id',
        'job_title_id',
        'employment_type',
        'current_headcount',
        'target_headcount',
        'vacancies_count',
        'fulfilled_count',
        'estimated_monthly_salary',
        'estimated_total_cost',
        'priority',
        'target_quarter',
        'justification',
    ];

    protected $casts = [
        'employment_type'          => EmploymentType::class,
        'current_headcount'        => 'integer',
        'target_headcount'         => 'integer',
        'vacancies_count'          => 'integer',
        'fulfilled_count'          => 'integer',
        'estimated_monthly_salary' => 'decimal:3',
        'estimated_total_cost'     => 'decimal:3',
        'priority'                 => PlanItemPriority::class,
        'target_quarter'           => PlanQuarter::class,
    ];

    /**
     * Parent workforce plan.
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(WorkforcePlan::class, 'workforce_plan_id');
    }

    /**
     * Department relation.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Branch relation.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
    }

    /**
     * Job title relation.
     */
    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class, 'job_title_id');
    }

    /**
     * Job requisitions linked to this plan item.
     */
    public function requisitions(): HasMany
    {
        return $this->hasMany(JobRequisition::class, 'workforce_plan_item_id');
    }

    /**
     * Hired / fulfilled count alias.
     */
    public function getHiredCountAttribute(): int
    {
        return (int) ($this->fulfilled_count ?? 0);
    }

    /**
     * Recalculate vacancies count and total cost.
     */
    public function recalculate(): void
    {
        $this->vacancies_count = max(0, $this->target_headcount - $this->current_headcount);
        // Annual estimated cost = target headcount * monthly salary * 12
        $this->estimated_total_cost = (float) ($this->target_headcount * $this->estimated_monthly_salary * 12);
        $this->saveQuietly();
    }
}
