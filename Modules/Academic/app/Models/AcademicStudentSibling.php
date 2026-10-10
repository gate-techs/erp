<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicStudentSibling extends Model
{
    use HasFactory;

    protected $table = 'academic_student_siblings';

    protected $fillable = [
        'academic_student_id',
        'sibling_student_id',
        'sibling_name',
        'academic_class_id',
        'class_name',
    ];

    protected $casts = [
        'academic_student_id' => 'integer',
        'sibling_student_id'  => 'integer',
        'academic_class_id'   => 'integer',
    ];

    //------------------------------------------
    public function student(): BelongsTo
    {
        return $this->belongsTo(AcademicStudent::class, 'academic_student_id');
    }

    //------------------------------------------
    public function siblingStudent(): BelongsTo
    {
        return $this->belongsTo(AcademicStudent::class, 'sibling_student_id');
    }

    //------------------------------------------
    public function academicClass(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'academic_class_id');
    }
}
