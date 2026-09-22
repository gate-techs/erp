<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AuditLog\Traits\Auditable;
use Modules\HR\Enums\LeaveRequestStatus;
use Modules\HR\Enums\LeaveType;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hr_leave_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'days_count',
        'replacement_employee_id',
        'status',
        'reason',
        'rejection_reason',
        'attachment',
        'emergency_contact_phone',
        'manager_approved_by',
        'manager_approved_at',
        'hr_approved_by',
        'hr_approved_at',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'leave_type'          => LeaveType::class,
        'status'              => LeaveRequestStatus::class,
        'start_date'          => 'date',
        'end_date'            => 'date',
        'days_count'          => 'float',
        'manager_approved_at' => 'datetime',
        'hr_approved_at'      => 'datetime',
    ];

    // =========================================================================
    // Relationships
    // =========================================================================

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function replacementEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'replacement_employee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function managerApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_approved_by');
    }

    public function hrApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hr_approved_by');
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopePending(Builder $query): Builder
    {
        return $query->whereIn('status', [LeaveRequestStatus::PENDING->value, LeaveRequestStatus::MANAGER_APPROVED->value]);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', LeaveRequestStatus::APPROVED->value);
    }

    public function scopeActiveOn(Builder $query, string $date): Builder
    {
        return $query->where('status', LeaveRequestStatus::APPROVED->value)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date);
    }
}
