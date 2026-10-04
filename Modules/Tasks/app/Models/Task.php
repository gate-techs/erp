<?php

namespace Modules\Tasks\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AuditLog\Traits\Auditable;
use Modules\HR\Models\Employee;
use Modules\Tasks\Enums\TaskPriority;
use Modules\Tasks\Enums\TaskStatus;

class Task extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable, Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tasks';

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public array $translatedAttributes = [
        'title',
        'body',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'task_group_id',
        'employee_id',
        'priority',
        'task_status',
        'start_date',
        'completion_date',
        'completed_at',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'priority'        => TaskPriority::class,
        'task_status'     => TaskStatus::class,
        'start_date'      => 'date',
        'completion_date' => 'date',
        'completed_at'    => 'datetime',
    ];

    //-----------------------------------------
    /**
     * Get the task group that owns the task.
     */
    public function taskGroup(): BelongsTo
    {
        return $this->belongsTo(TaskGroup::class, 'task_group_id');
    }

    //-----------------------------------------
    /**
     * Get the employee assigned to the task.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    //-----------------------------------------
    /**
     * Get the user who created the task.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //-----------------------------------------
    /**
     * Get all attachments associated with the task.
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(TaskAttachment::class, 'task_id');
    }

    //-----------------------------------------
    /**
     * Get all status change histories for the task.
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(TaskStatusHistory::class, 'task_id')->latest('id');
    }

    //-----------------------------------------
    /**
     * Get all notes / comments associated with the task.
     */
    public function notes(): HasMany
    {
        return $this->hasMany(TaskNote::class, 'task_id')->latest('id');
    }

    //-----------------------------------------
    /**
     * Scope a query to only include pending tasks.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('task_status', TaskStatus::PENDING);
    }

    //-----------------------------------------
    /**
     * Scope a query to only include in-progress tasks.
     */
    public function scopeInProgress(Builder $query): Builder
    {
        return $query->where('task_status', TaskStatus::IN_PROGRESS);
    }

    //-----------------------------------------
    /**
     * Scope a query to only include completed tasks.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('task_status', TaskStatus::COMPLETED);
    }

    //-----------------------------------------
    /**
     * Scope a query to filter by task group.
     */
    public function scopeByGroup(Builder $query, int $groupId): Builder
    {
        return $query->where('task_group_id', $groupId);
    }

    //-----------------------------------------
    /**
     * Scope a query to filter by employee.
     */
    public function scopeByEmployee(Builder $query, int $employeeId): Builder
    {
        return $query->where('employee_id', $employeeId);
    }

    //-----------------------------------------
    /**
     * Get remaining days badge / label for task due date.
     */
    public function getRemainingDaysBadgeAttribute(): string
    {
        if ($this->task_status === TaskStatus::COMPLETED) {
            return '<span class="badge bg-label-success d-inline-flex align-items-center gap-1 font-11">
                <i class="icon-base bx bx-check-circle font-12"></i> ' . __('Completed') . '
            </span>';
        }

        if (!$this->completion_date) {
            return '<span class="text-muted small">' . __('No deadline') . '</span>';
        }

        $now = now()->startOfDay();
        $dueDate = $this->completion_date->startOfDay();
        $diff = (int) $now->diffInDays($dueDate, false);

        if ($diff > 0) {
            $class = $diff <= 2 ? 'bg-label-warning' : 'bg-label-info';
            return '<span class="badge ' . $class . ' d-inline-flex align-items-center gap-1 font-11">
                <i class="icon-base bx bx-time font-12"></i> ' . __('Remaining: :days days', ['days' => $diff]) . '
            </span>';
        } elseif ($diff === 0) {
            return '<span class="badge bg-label-warning d-inline-flex align-items-center gap-1 font-11">
                <i class="icon-base bx bx-alarm-exclamation font-12"></i> ' . __('Due Today') . '
            </span>';
        } else {
            $absDiff = abs($diff);
            return '<span class="badge bg-label-danger d-inline-flex align-items-center gap-1 font-11">
                <i class="icon-base bx bx-error font-12"></i> ' . __('Overdue by :days days', ['days' => $absDiff]) . '
            </span>';
        }
    }

    //-----------------------------------------
    /**
     * Get plain text for remaining days.
     */
    public function getRemainingDaysTextAttribute(): string
    {
        if ($this->task_status === TaskStatus::COMPLETED) {
            return __('Completed');
        }

        if (!$this->completion_date) {
            return __('No deadline');
        }

        $now = now()->startOfDay();
        $dueDate = $this->completion_date->startOfDay();
        $diff = (int) $now->diffInDays($dueDate, false);

        if ($diff > 0) {
            return __('Remaining: :days days', ['days' => $diff]);
        } elseif ($diff === 0) {
            return __('Due Today');
        } else {
            return __('Overdue by :days days', ['days' => abs($diff)]);
        }
    }
}
