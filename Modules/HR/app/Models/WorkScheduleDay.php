<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkScheduleDay extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'work_schedule_days';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'work_schedule_id',
        'day_of_week',
        'is_off_day',
        'shift_id',
        'second_shift_id',
        'work_hours',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'day_of_week' => 'integer',
        'is_off_day'  => 'boolean',
        'work_hours'  => 'decimal:2',
    ];

    //======================
    /**
     * Relationship: Work schedule header.
     */
    public function workSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class, 'work_schedule_id');
    }

    //======================
    /**
     * Relationship: Primary Shift for this day.
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id');
    }

    //======================
    /**
     * Relationship: Optional secondary/split Shift.
     */
    public function secondShift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'second_shift_id');
    }

    //======================
    /**
     * Get day name in Arabic/English.
     */
    public function dayName(): string
    {
        return self::dayNames()[$this->day_of_week] ?? '';
    }

    //======================
    /**
     * Days of week dictionary (0=Sunday to 6=Saturday).
     *
     * @return array<int, string>
     */
    public static function dayNames(): array
    {
        return [
            6 => __('Saturday'),
            0 => __('Sunday'),
            1 => __('Monday'),
            2 => __('Tuesday'),
            3 => __('Wednesday'),
            4 => __('Thursday'),
            5 => __('Friday'),
        ];
    }
}
