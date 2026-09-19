<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\PenaltyCalculationType;
use Modules\HR\Enums\PenaltyDeductionMethod;
use Modules\HR\Enums\PenaltyStatus;

class Penalty extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hr_penalties';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'penalty_number',
        'employee_id',
        'penalty_type_id',
        'calculation_type',
        'calculation_value',
        'amount',
        'penalty_date',
        'deduction_method',
        'payroll_month',
        'status',
        'reason',
        'notes',
        'attachment',
        'cancellation_reason',
        'approved_by',
        'approved_at',
        'deducted_by',
        'deducted_at',
        'cancelled_by',
        'cancelled_at',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status'            => PenaltyStatus::class,
        'calculation_type'  => PenaltyCalculationType::class,
        'deduction_method'  => PenaltyDeductionMethod::class,
        'penalty_date'      => 'date',
        'calculation_value' => 'decimal:2',
        'amount'            => 'decimal:2',
        'approved_at'       => 'datetime',
        'deducted_at'       => 'datetime',
        'cancelled_at'      => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Penalty $penalty) {
            if (empty($penalty->penalty_number)) {
                $yearMonth = now()->format('Ym');
                $lastPenalty = static::withTrashed()
                    ->where('penalty_number', 'like', "PEN-{$yearMonth}-%")
                    ->orderByDesc('id')
                    ->first();

                $seq = 1;
                if ($lastPenalty && preg_match('/PEN-\d{6}-(\d{4})/', $lastPenalty->penalty_number, $matches)) {
                    $seq = (int) $matches[1] + 1;
                }

                $penalty->penalty_number = sprintf('PEN-%s-%04d', $yearMonth, $seq);
            }
        });
    }

    //======================
    // Relationships
    //======================

    /**
     * The employee who received the penalty.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * The penalty type from hr_settings.
     */
    public function penaltyType(): BelongsTo
    {
        return $this->belongsTo(HrSetting::class, 'penalty_type_id');
    }

    /**
     * User who created the penalty record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who approved the penalty.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * User who marked the penalty as deducted.
     */
    public function deductor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deducted_by');
    }

    /**
     * User who cancelled the penalty.
     */
    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    //======================
    // Scopes
    //======================

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', PenaltyStatus::PENDING->value);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', PenaltyStatus::APPROVED->value);
    }

    public function scopeDeducted(Builder $query): Builder
    {
        return $query->where('status', PenaltyStatus::DEDUCTED->value);
    }

    public function scopeForPayroll(Builder $query, string $month): Builder
    {
        return $query->where('deduction_method', PenaltyDeductionMethod::NEXT_PAYROLL->value)
            ->where('payroll_month', $month)
            ->where('status', PenaltyStatus::APPROVED->value);
    }
}
