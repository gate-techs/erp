<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Model;

class AcademicLessonTranslation extends Model
{
    public $timestamps = false;

    protected $table = 'academic_lesson_translations';

    protected $fillable = [
        'title',
        'content',
    ];
}
