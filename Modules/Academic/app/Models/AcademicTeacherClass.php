<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\HR\Models\Employee;

class AcademicTeacherClass extends Model
{
    use HasFactory;

    protected $table = 'academic_teacher_classes';

    protected $fillable = [
        'employee_id',
        'academic_stage_id',
        'academic_class_id',
    ];

    protected $casts = [
        'employee_id' => 'integer',
        'academic_stage_id' => 'integer',
        'academic_class_id' => 'integer',
    ];

    //------------------------------------------
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    //------------------------------------------
    public function stage(): BelongsTo
    {
        return $this->belongsTo(AcademicStage::class, 'academic_stage_id');
    }

    //------------------------------------------
    public function class(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'academic_class_id');
    }
}
