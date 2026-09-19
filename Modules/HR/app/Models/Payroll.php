<?php

namespace Modules\HR\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\PayrollStatus;
use Modules\Nursery\Models\NurseryBranch;

class Payroll extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hr_payrolls';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payroll_number',
        'payroll_month',
        'title',
        'branch_id',
        'total_basic_salary',
        'total_allowances',
        'total_overtime',
        'total_rewards',
        'total_gross',
        'total_deductions',
        'total_loans_deduction',
        'total_penalties_deduction',
        'total_absence_deduction',
        'total_net',
        'employees_count',
        'status',
        'notes',
        'approved_by',
        'approved_at',
        'paid_by',
        'paid_at',
        'cancelled_by',
        'cancelled_at',
        'cancellation_reason',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status'                    => PayrollStatus::class,
        'total_basic_salary'        => 'decimal:2',
        'total_allowances'          => 'decimal:2',
        'total_overtime'            => 'decimal:2',
        'total_rewards'             => 'decimal:2',
        'total_gross'               => 'decimal:2',
        'total_deductions'          => 'decimal:2',
        'total_loans_deduction'     => 'decimal:2',
        'total_penalties_deduction' => 'decimal:2',
        'total_absence_deduction'   => 'decimal:2',
        'total_net'                 => 'decimal:2',
        'employees_count'           => 'integer',
        'approved_at'               => 'datetime',
        'paid_at'                   => 'datetime',
        'cancelled_at'              => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (Payroll $payroll) {
            if (empty($payroll->payroll_number)) {
                $cleanMonth = str_replace('-', '', $payroll->payroll_month ?: now()->format('Ym'));
                $lastPayroll = static::withTrashed()
                    ->where('payroll_number', 'like', "PAY-{$cleanMonth}-%")
                    ->orderByDesc('id')
                    ->first();

                $seq = 1;
                if ($lastPayroll && preg_match('/PAY-\d{6}-(\d{4})/', $lastPayroll->payroll_number, $matches)) {
                    $seq = (int) $matches[1] + 1;
                }

                $payroll->payroll_number = sprintf('PAY-%s-%04d', $cleanMonth, $seq);
            }
        });
    }

    //======================
    // Relationships
    //======================

    /**
     * Relationship: Payroll Items for employees.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PayrollItem::class, 'payroll_id');
    }

    /**
     * Relationship: Optional specific branch.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
    }

    /**
     * Relationship: User who approved.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Relationship: User who disbursed/paid.
     */
    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    /**
     * Relationship: User who cancelled.
     */
    public function canceller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    /**
     * Relationship: User who created.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //======================
    // Scopes
    //======================

    public function scopeMonth(Builder $query, string $month): Builder
    {
        return $query->where('payroll_month', $month);
    }

    public function scopeStatus(Builder $query, PayrollStatus|string $status): Builder
    {
        $val = $status instanceof PayrollStatus ? $status->value : $status;
        return $query->where('status', $val);
    }

    public function scopeBranch(Builder $query, int $branchId): Builder
    {
        return $query->where('branch_id', $branchId);
    }
}
