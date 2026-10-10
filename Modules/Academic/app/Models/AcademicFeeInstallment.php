<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicFeeInstallment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_PAID = 'paid';
    public const STATUS_OVERDUE = 'overdue';

    protected $table = 'academic_fee_installments';

    protected $fillable = [
        'academic_student_fee_id',
        'installment_number',
        'due_date',
        'amount',
        'paid_amount',
        'status',
    ];

    protected $casts = [
        'academic_student_fee_id' => 'integer',
        'installment_number' => 'integer',
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function studentFee(): BelongsTo
    {
        return $this->belongsTo(AcademicStudentFee::class, 'academic_student_fee_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(AcademicFeePayment::class, 'academic_fee_installment_id');
    }
}
