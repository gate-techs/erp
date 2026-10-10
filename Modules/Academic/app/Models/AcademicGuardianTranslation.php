<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicGuardianTranslation extends Model
{
    use HasFactory;

    protected $table = 'academic_guardian_translations';

    protected $fillable = [
        'name',
    ];
}
