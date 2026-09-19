<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\RewardCalculationType;
use Modules\HR\Enums\RewardPaymentMethod;
use Modules\HR\Enums\RewardStatus;

class Reward extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hr_rewards';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reward_number',
        'employee_id',
        'reward_type_id',
        'calculation_type',
        'calculation_value',
        'amount',
        'award_date',
        'payment_method',
        'payroll_month',
        'status',
        'reason',
        'notes',
        'attachment',
        'rejection_reason',
        'approved_by',
        'approved_at',
        'paid_by',
        'paid_at',
        'rejected_by',
        'rejected_at',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status'            => RewardStatus::class,
        'calculation_type'  => RewardCalculationType::class,
        'payment_method'    => RewardPaymentMethod::class,
        'award_date'        => 'date',
        'calculation_value' => 'decimal:2',
        'amount'            => 'decimal:2',
        'approved_at'       => 'datetime',
        'paid_at'           => 'datetime',
        'rejected_at'       => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Reward $reward) {
            if (empty($reward->reward_number)) {
                $yearMonth = now()->format('Ym');
                $lastReward = static::withTrashed()
                    ->where('reward_number', 'like', "RWD-{$yearMonth}-%")
                    ->orderByDesc('id')
                    ->first();

                $seq = 1;
                if ($lastReward && preg_match('/RWD-\d{6}-(\d{4})/', $lastReward->reward_number, $matches)) {
                    $seq = (int) $matches[1] + 1;
                }

                $reward->reward_number = sprintf('RWD-%s-%04d', $yearMonth, $seq);
            }
        });
    }

    //======================
    // Relationships
    //======================

    /**
     * The employee who received the reward.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * The reward type from hr_settings.
     */
    public function rewardType(): BelongsTo
    {
        return $this->belongsTo(HrSetting::class, 'reward_type_id');
    }

    /**
     * User who created the reward record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who approved the reward.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * User who marked the reward as paid.
     */
    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * User who rejected the reward.
     */
    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    //======================
    // Scopes
    //======================

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', RewardStatus::PENDING->value);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', RewardStatus::APPROVED->value);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', RewardStatus::PAID->value);
    }

    public function scopeForPayroll(Builder $query, string $month): Builder
    {
        return $query->where('payment_method', RewardPaymentMethod::NEXT_PAYROLL->value)
            ->where('payroll_month', $month)
            ->where('status', RewardStatus::APPROVED->value);
    }
}
