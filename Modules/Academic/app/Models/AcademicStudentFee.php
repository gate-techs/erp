<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicStudentFee extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    protected $table = 'academic_student_fees';

    protected $fillable = [
        'academic_student_id',
        'academic_fee_id',
        'total_amount',
        'installments_count',
        'installment_amount',
        'start_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'academic_student_id' => 'integer',
        'academic_fee_id' => 'integer',
        'total_amount' => 'decimal:2',
        'installments_count' => 'integer',
        'installment_amount' => 'decimal:2',
        'start_date' => 'date',
        'created_by' => 'integer',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(AcademicStudent::class, 'academic_student_id');
    }

    public function fee(): BelongsTo
    {
        return $this->belongsTo(AcademicFee::class, 'academic_fee_id');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(AcademicFeeInstallment::class, 'academic_student_fee_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(AcademicFeePayment::class, 'academic_student_fee_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
