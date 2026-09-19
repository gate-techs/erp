<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HR\Enums\PayrollPaymentStatus;

class PayrollItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hr_payroll_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'payroll_id',
        'employee_id',
        'basic_salary',
        'allowances_amount',
        'allowances_breakdown',
        'overtime_hours',
        'overtime_amount',
        'rewards_amount',
        'other_earnings',
        'gross_salary',
        'absence_days',
        'absence_deduction',
        'late_minutes',
        'late_deduction',
        'loans_deduction',
        'penalties_deduction',
        'insurance_deduction',
        'other_deductions',
        'total_deductions',
        'net_salary',
        'bank_name',
        'account_number',
        'iban',
        'payment_status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_status'      => PayrollPaymentStatus::class,
        'allowances_breakdown'=> 'array',
        'basic_salary'        => 'decimal:2',
        'allowances_amount'   => 'decimal:2',
        'overtime_hours'      => 'decimal:2',
        'overtime_amount'     => 'decimal:2',
        'rewards_amount'      => 'decimal:2',
        'other_earnings'      => 'decimal:2',
        'gross_salary'        => 'decimal:2',
        'absence_days'        => 'decimal:2',
        'absence_deduction'   => 'decimal:2',
        'late_minutes'        => 'integer',
        'late_deduction'      => 'decimal:2',
        'loans_deduction'     => 'decimal:2',
        'penalties_deduction' => 'decimal:2',
        'insurance_deduction' => 'decimal:2',
        'other_deductions'    => 'decimal:2',
        'total_deductions'    => 'decimal:2',
        'net_salary'          => 'decimal:2',
    ];

    //======================
    // Relationships
    //======================

    /**
     * Relationship: Parent Payroll run.
     */
    public function payroll(): BelongsTo
    {
        return $this->belongsTo(Payroll::class, 'payroll_id');
    }

    /**
     * Relationship: Employee.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
