<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AuditLog\Traits\Auditable;
use Modules\HR\Enums\ExitClearanceStatus;

class ExitClearance extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $table = 'hr_exit_clearances';

    protected $fillable = [
        'clearance_code',
        'employee_id',
        'department_id',
        'job_title_id',
        'resignation_date',
        'last_working_day',
        'leaving_reason',
        'reason_details',
        'status',
        'outstanding_loans_amount',
        'leave_balance_days',
        'end_of_service_amount',
        'net_settlement_amount',
        'final_approved_by',
        'final_approved_at',
        'final_notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'resignation_date'         => 'date',
        'last_working_day'         => 'date',
        'status'                   => ExitClearanceStatus::class,
        'outstanding_loans_amount' => 'decimal:2',
        'leave_balance_days'       => 'decimal:2',
        'end_of_service_amount'    => 'decimal:2',
        'net_settlement_amount'    => 'decimal:2',
        'final_approved_at'        => 'datetime',
    ];

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

    public function approvals(): HasMany
    {
        return $this->hasMany(ExitClearanceApproval::class, 'exit_clearance_id')->orderBy('sort_order');
    }

    public function finalApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'final_approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

