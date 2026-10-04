<?php

namespace Modules\Tasks\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AuditLog\Traits\Auditable;
use Modules\HR\Models\Department;

class TaskGroup extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable, Auditable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'task_groups';

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public array $translatedAttributes = [
        'name',
        'description',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'department_id',
        'start_date',
        'end_date',
        'is_active',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'is_active'  => 'boolean',
    ];

    //-----------------------------------------
    /**
     * Get the department that owns the task group.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    //-----------------------------------------
    /**
     * Get the user who created the task group.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //-----------------------------------------
    /**
     * Scope a query to only include active task groups.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    //-----------------------------------------
    /**
     * Get all tasks associated with the task group.
     */
    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class, 'task_group_id');
    }

    //-----------------------------------------
    /**
     * Get remaining days badge / label.
     */
    public function getRemainingDaysBadgeAttribute(): string
    {
        if (!$this->end_date) {
            return '<span class="text-muted small">' . __('No deadline') . '</span>';
        }

        $now = now()->startOfDay();
        $endDate = $this->end_date->startOfDay();
        $diff = (int) $now->diffInDays($endDate, false);

        if ($diff > 0) {
            $class = $diff <= 3 ? 'bg-label-warning' : 'bg-label-success';
            return '<span class="badge ' . $class . ' d-inline-flex align-items-center gap-1 font-11">
                <i class="icon-base bx bx-time-five font-12"></i> ' . __('Remaining: :days days', ['days' => $diff]) . '
            </span>';
        } elseif ($diff === 0) {
            return '<span class="badge bg-label-warning d-inline-flex align-items-center gap-1 font-11">
                <i class="icon-base bx bx-alarm-exclamation font-12"></i> ' . __('Ends Today') . '
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
        if (!$this->end_date) {
            return __('No deadline');
        }

        $now = now()->startOfDay();
        $endDate = $this->end_date->startOfDay();
        $diff = (int) $now->diffInDays($endDate, false);

        if ($diff > 0) {
            return __('Remaining: :days days', ['days' => $diff]);
        } elseif ($diff === 0) {
            return __('Ends Today');
        } else {
            return __('Overdue by :days days', ['days' => abs($diff)]);
        }
    }
}
