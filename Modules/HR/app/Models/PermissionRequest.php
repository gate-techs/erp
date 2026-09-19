<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\PermissionRequestStatus;
use Modules\HR\Enums\PermissionType;

class PermissionRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_permission_requests';

    protected $fillable = [
        'employee_id',
        'date',
        'type',
        'from_time',
        'to_time',
        'hours_count',
        'status',
        'is_emergency',
        'reason',
        'rejection_reason',
        'attachment',
        'financial_policy',
        'created_by',
        'manager_approved_by',
        'manager_approved_at',
        'hr_approved_by',
        'hr_approved_at',
    ];

    protected $casts = [
        'date'                => 'date',
        'type'                => PermissionType::class,
        'status'              => PermissionRequestStatus::class,
        'hours_count'         => 'decimal:2',
        'is_emergency'        => 'boolean',
        'manager_approved_at' => 'datetime',
        'hr_approved_at'      => 'datetime',
    ];

    //======================
    /**
     * Relationship: The employee requesting permission.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    //======================
    /**
     * Relationship: The user who created the record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //======================
    /**
     * Relationship: Direct manager who approved.
     */
    public function managerApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_approved_by');
    }

    //======================
    /**
     * Relationship: HR officer who approved.
     */
    public function hrApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hr_approved_by');
    }

    //======================
    /**
     * Scope: Filter pending approval.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', PermissionRequestStatus::PENDING->value);
    }

    //======================
    /**
     * Scope: Filter approved requests.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', PermissionRequestStatus::APPROVED->value);
    }

    //======================
    /**
     * Scope: Filter by a specific date.
     */
    public function scopeOnDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('date', $date);
    }

    //======================
    /**
     * Scope: Filter requests in a specific month and year.
     */
    public function scopeInMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    //======================
    /**
     * Formatted duration helper (e.g. "1 hr 30 mins").
     */
    public function getFormattedDurationAttribute(): string
    {
        $totalMinutes = round($this->hours_count * 60);
        $hours = intdiv($totalMinutes, 60);
        $minutes = $totalMinutes % 60;

        $parts = [];
        if ($hours > 0) {
            $parts[] = $hours . ' ' . __('hr');
        }
        if ($minutes > 0) {
            $parts[] = $minutes . ' ' . __('min');
        }

        return !empty($parts) ? implode(' ', $parts) : '0 ' . __('min');
    }
}
