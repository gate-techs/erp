<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicFeePayment extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PAID = 'paid';
    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PENDING = 'pending';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';

    protected $table = 'academic_fee_payments';

    protected $fillable = [
        'academic_student_fee_id',
        'academic_fee_installment_id',
        'amount',
        'payment_date',
        'invoice_number',
        'payment_number',
        'transaction_number',
        'reference_number',
        'tracking_number',
        'status',
        'created_by',
    ];

    protected $casts = [
        'academic_student_fee_id' => 'integer',
        'academic_fee_installment_id' => 'integer',
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'created_by' => 'integer',
    ];

    public function studentFee(): BelongsTo
    {
        return $this->belongsTo(AcademicStudentFee::class, 'academic_student_fee_id');
    }

    public function installment(): BelongsTo
    {
        return $this->belongsTo(AcademicFeeInstallment::class, 'academic_fee_installment_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
