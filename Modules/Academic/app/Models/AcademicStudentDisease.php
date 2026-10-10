<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Academic\Enums\DiseaseType;

class AcademicStudentDisease extends Model
{
    use HasFactory;

    protected $table = 'academic_student_diseases';

    protected $fillable = [
        'academic_student_id',
        'disease_code',
        'has_disease',
        'medication_name',
        'notes',
    ];

    protected $casts = [
        'academic_student_id' => 'integer',
        'disease_code'        => DiseaseType::class,
        'has_disease'         => 'boolean',
    ];

    protected $appends = [
        'disease_label',
    ];

    //------------------------------------------
    public function student(): BelongsTo
    {
        return $this->belongsTo(AcademicStudent::class, 'academic_student_id');
    }

    //------------------------------------------
    public function getDiseaseLabelAttribute(): string
    {
        return $this->disease_code instanceof DiseaseType
            ? $this->disease_code->label()
            : '-';
    }
}
