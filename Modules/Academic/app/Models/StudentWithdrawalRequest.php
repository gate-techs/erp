<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;

class StudentWithdrawalRequest extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = ['institution_id','branch_id','academic_student_id','academic_class_id','academic_guardian_id','civil_id_snapshot','request_date','effective_withdrawal_date','reason','parent_signature','branch_manager_signature','parent_financial_obligations','management_report','status','reviewed_by','reviewed_at','rejection_reason','created_by'];
    protected $casts = ['request_date'=>'date','effective_withdrawal_date'=>'date','reviewed_at'=>'datetime'];

    public function institution(): BelongsTo { return $this->belongsTo(Nursery::class, 'institution_id'); }
    public function branch(): BelongsTo { return $this->belongsTo(NurseryBranch::class, 'branch_id'); }
    public function student(): BelongsTo { return $this->belongsTo(AcademicStudent::class, 'academic_student_id'); }
    public function academicClass(): BelongsTo { return $this->belongsTo(AcademicClass::class, 'academic_class_id'); }
    public function guardian(): BelongsTo { return $this->belongsTo(AcademicGuardian::class, 'academic_guardian_id'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
