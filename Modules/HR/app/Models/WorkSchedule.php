<?php

namespace Modules\HR\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\WorkScheduleStatus;
use Modules\HR\Enums\WorkScheduleType;
use Modules\Nursery\Models\NurseryBranch;

class WorkSchedule extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'work_schedules';

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
        'code',
        'type',
        'branch_id',
        'department_id',
        'working_days_count',
        'total_weekly_hours',
        'is_default',
        'color',
        'status',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type'               => WorkScheduleType::class,
        'status'             => WorkScheduleStatus::class,
        'is_default'         => 'boolean',
        'working_days_count' => 'integer',
        'total_weekly_hours' => 'decimal:2',
    ];

    //======================
    /**
     * Relationship: Schedule days.
     */
    public function days(): HasMany
    {
        return $this->hasMany(WorkScheduleDay::class, 'work_schedule_id')->orderBy('day_of_week');
    }

    //======================
    /**
     * Relationship: Associated Department (Optional).
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    //======================
    /**
     * Relationship: Associated Branch (Optional).
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
    }

    //======================
    /**
     * Relationship: User who created the schedule.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
