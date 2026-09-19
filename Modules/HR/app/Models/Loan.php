<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\LoanStatus;
use Modules\HR\Enums\LoanType;

class Loan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hr_loans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'created_by',
        'type',
        'amount',
        'installments_count',
        'monthly_installment',
        'start_date',
        'total_paid',
        'remaining_amount',
        'status',
        'reason',
        'rejection_reason',
        'attachment',
        'disbursed_at',
        'disbursement_method',
        'disbursement_reference',
        'manager_approved_by',
        'manager_approved_at',
        'hr_approved_by',
        'hr_approved_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type'                => LoanType::class,
        'status'              => LoanStatus::class,
        'amount'              => 'decimal:2',
        'installments_count'  => 'integer',
        'monthly_installment' => 'decimal:2',
        'total_paid'          => 'decimal:2',
        'remaining_amount'    => 'decimal:2',
        'start_date'          => 'date',
        'disbursed_at'        => 'datetime',
        'manager_approved_at' => 'datetime',
        'hr_approved_at'      => 'datetime',
    ];

    //======================
    // Relationships
    //======================

    /**
     * Employee who requested the loan.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * User who created the loan record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Manager who approved the loan.
     */
    public function managerApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_approved_by');
    }

    /**
     * HR user who approved the loan.
     */
    public function hrApprover(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hr_approved_by');
    }

    /**
     * All installments belonging to this loan.
     */
    public function installments(): HasMany
    {
        return $this->hasMany(LoanInstallment::class, 'loan_id')->orderBy('installment_number');
    }

    //======================
    // Scopes
    //======================

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', LoanStatus::PENDING->value);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', LoanStatus::APPROVED->value);
    }

    public function scopeDisbursed(Builder $query): Builder
    {
        return $query->where('status', LoanStatus::DISBURSED->value);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', LoanStatus::PAID->value);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [LoanStatus::APPROVED->value, LoanStatus::DISBURSED->value]);
    }

    //======================
    // Accessors
    //======================

    /**
     * Calculate repayment progress percentage (0 - 100%).
     */
    public function getProgressPercentageAttribute(): int
    {
        if ($this->amount <= 0) {
            return 0;
        }

        $percentage = ($this->total_paid / $this->amount) * 100;
        return (int) min(100, round($percentage));
    }

    /**
     * Check if the loan is fully paid.
     */
    public function getIsFullyPaidAttribute(): bool
    {
        return $this->remaining_amount <= 0 || $this->status === LoanStatus::PAID;
    }
}
