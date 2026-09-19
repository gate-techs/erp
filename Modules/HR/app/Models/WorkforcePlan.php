<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\WorkforcePlanStatus;

class WorkforcePlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_workforce_plans';

    protected $fillable = [
        'plan_code',
        'title',
        'year',
        'start_date',
        'end_date',
        'status',
        'total_target_headcount',
        'total_budgeted_cost',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'year'                   => 'integer',
        'start_date'             => 'date',
        'end_date'               => 'date',
        'status'                 => WorkforcePlanStatus::class,
        'total_target_headcount' => 'integer',
        'total_budgeted_cost'    => 'decimal:3',
        'approved_at'            => 'datetime',
    ];

    /**
     * Description alias for notes.
     */
    public function getDescriptionAttribute(): ?string
    {
        return $this->notes;
    }

    public function setDescriptionAttribute(?string $value): void
    {
        $this->attributes['notes'] = $value;
    }

    /**
     * Plan items relation.
     */
    public function items(): HasMany
    {
        return $this->hasMany(WorkforcePlanItem::class, 'workforce_plan_id');
    }

    /**
     * User who created the plan.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who approved the plan.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Recalculate totals from items.
     */
    public function recalculateTotals(): void
    {
        $this->total_target_headcount = (int) $this->items()->sum('target_headcount');
        $this->total_budgeted_cost    = (float) $this->items()->sum('estimated_total_cost');
        $this->saveQuietly();
    }

    /**
     * Get overall fulfillment percentage.
     */
    public function getFulfillmentPercentageAttribute(): float
    {
        if ($this->total_target_headcount <= 0) {
            return 0.0;
        }

        $totalFulfilled = (int) $this->items()->sum('fulfilled_count');
        return round(($totalFulfilled / $this->total_target_headcount) * 100, 1);
    }
}
