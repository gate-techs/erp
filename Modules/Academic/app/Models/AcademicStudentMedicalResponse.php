<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Academic\Enums\MedicalQuestionnaire1;

class AcademicStudentMedicalResponse extends Model
{
    use HasFactory;

    protected $table = 'academic_student_medical_responses';

    protected $fillable = [
        'academic_student_id',
        'question_code',
        'answer',
        'details',
    ];

    protected $casts = [
        'academic_student_id' => 'integer',
        'question_code'       => MedicalQuestionnaire1::class,
        'answer'              => 'boolean',
    ];

    protected $appends = [
        'question_text',
    ];

    //------------------------------------------
    public function student(): BelongsTo
    {
        return $this->belongsTo(AcademicStudent::class, 'academic_student_id');
    }

    //------------------------------------------
    public function getQuestionTextAttribute(): string
    {
        return $this->question_code instanceof MedicalQuestionnaire1
            ? $this->question_code->question()
            : '-';
    }
}
