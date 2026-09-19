<?php

namespace Modules\HR\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HR\Enums\InstallmentStatus;

class LoanInstallment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hr_loan_installments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'loan_id',
        'installment_number',
        'due_date',
        'amount',
        'paid_amount',
        'status',
        'paid_at',
        'postponed_from',
        'notes',
        'payroll_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status'             => InstallmentStatus::class,
        'installment_number' => 'integer',
        'amount'             => 'decimal:2',
        'paid_amount'        => 'decimal:2',
        'due_date'           => 'date',
        'paid_at'            => 'date',
        'postponed_from'     => 'date',
    ];

    //======================
    // Relationships
    //======================

    /**
     * Loan this installment belongs to.
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }

    //======================
    // Scopes
    //======================

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', InstallmentStatus::PENDING->value);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('status', InstallmentStatus::PAID->value);
    }

    public function scopeDueInMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('due_date', $year)
                     ->whereMonth('due_date', $month);
    }
}
