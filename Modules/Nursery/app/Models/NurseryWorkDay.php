<?php

namespace Modules\Nursery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Nursery\Enums\DayOfWeek;

class NurseryWorkDay extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nursery_work_days';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nursery_id',
        'day',
        'from_time',
        'to_time',
        'is_off',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'day' => DayOfWeek::class,
        'is_off' => 'boolean',
    ];

    //======================
    /**
     * Get parent nursery.
     */
    public function nursery(): BelongsTo
    {
        return $this->belongsTo(Nursery::class);
    }

    //======================
    /**
     * Get localized day name.
     */
    public function getDayNameAttribute(): string
    {
        return $this->day instanceof DayOfWeek ? $this->day->label() : (string) $this->day;
    }

    //======================
    /**
     * Formatted time range or Holiday badge.
     */
    public function getWorkingHoursAttribute(): string
    {
        if ($this->is_off) {
            return __('Holiday / Off');
        }

        $from = $this->from_time ? date('h:i A', strtotime($this->from_time)) : '--:--';
        $to = $this->to_time ? date('h:i A', strtotime($this->to_time)) : '--:--';

        return "{$from} - {$to}";
    }
}
