<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MedicationRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'medication_requests';
    protected $fillable = ['parent_id','student_id','medicine_name','dosage_amount','dosage_time','date','nurse_id','nurse_note','action_time','done','created_by','updated_by','deleted_by'];
    protected $casts = ['date'=>'date','action_time'=>'datetime','done'=>'boolean'];

    public function parent(): BelongsTo { return $this->belongsTo(User::class, 'parent_id'); }
    public function student(): BelongsTo { return $this->belongsTo(AcademicStudent::class, 'student_id'); }
    public function nurse(): BelongsTo { return $this->belongsTo(User::class, 'nurse_id'); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function updater(): BelongsTo { return $this->belongsTo(User::class, 'updated_by'); }
    public function deleter(): BelongsTo { return $this->belongsTo(User::class, 'deleted_by'); }
}
