<?php

namespace Modules\HR\Models;

use Modules\Nursery\Models\NurseryBranch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\EmploymentType;
use Modules\HR\Enums\JobRequisitionStatus;
use Modules\HR\Enums\PlanItemPriority;
use Modules\HR\Enums\RequisitionType;

class JobRequisition extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_job_requisitions';

    protected $fillable = [
        'requisition_number',
        'workforce_plan_item_id',
        'is_budgeted',
        'department_id',
        'branch_id',
        'job_title_id',
        'requested_by',
        'requisition_type',
        'replacement_for_employee_id',
        'headcount_needed',
        'hired_count',
        'employment_type',
        'expected_start_date',
        'min_salary',
        'max_salary',
        'currency',
        'required_experience_years',
        'education_level',
        'job_description',
        'responsibilities',
        'requirements',
        'justification',
        'status',
        'priority',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'is_budgeted'                => 'boolean',
        'requisition_type'           => RequisitionType::class,
        'headcount_needed'           => 'integer',
        'hired_count'                => 'integer',
        'employment_type'            => EmploymentType::class,
        'expected_start_date'        => 'date',
        'min_salary'                 => 'decimal:3',
        'max_salary'                 => 'decimal:3',
        'required_experience_years'  => 'integer',
        'status'                     => JobRequisitionStatus::class,
        'priority'                   => PlanItemPriority::class,
        'approved_at'                => 'datetime',
    ];

    /**
     * Linked workforce plan item.
     */
    public function planItem(): BelongsTo
    {
        return $this->belongsTo(WorkforcePlanItem::class, 'workforce_plan_item_id');
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
     * Requester relation (user).
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Replacement employee if replacing an existing employee.
     */
    public function replacementEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'replacement_for_employee_id');
    }

    /**
     * Approver user.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Creator user.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if requisition is fully fulfilled.
     */
    public function isFullyFulfilled(): bool
    {
        return $this->hired_count >= $this->headcount_needed;
    }
}
