<?php

namespace Modules\Nursery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NurseryBranchWorkDay extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nursery_branch_work_days';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nursery_branch_id',
        'day_name',
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
        'is_off' => 'boolean',
    ];

    //======================
    /**
     * Get parent nursery branch.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'nursery_branch_id');
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
