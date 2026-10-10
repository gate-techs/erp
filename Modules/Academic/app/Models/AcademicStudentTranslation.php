<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicStudentTranslation extends Model
{
    use HasFactory;

    protected $table = 'academic_student_translations';

    protected $fillable = [
        'name',
    ];
}
