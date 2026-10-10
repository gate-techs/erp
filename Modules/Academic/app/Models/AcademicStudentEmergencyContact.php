<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicStudentEmergencyContact extends Model
{
    use HasFactory;

    protected $table = 'academic_student_emergency_contacts';

    protected $fillable = [
        'academic_student_id',
        'priority',
        'contact_name',
        'relationship',
        'phone_1',
        'phone_2',
        'notes',
    ];

    protected $casts = [
        'academic_student_id' => 'integer',
        'priority'            => 'integer',
    ];

    //------------------------------------------
    public function student(): BelongsTo
    {
        return $this->belongsTo(AcademicStudent::class, 'academic_student_id');
    }
}
