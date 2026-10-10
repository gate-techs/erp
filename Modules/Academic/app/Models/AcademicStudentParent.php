<?php

namespace Modules\Academic\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcademicStudentParent extends Model
{
    use HasFactory;

    public const TYPE_FATHER = 1;
    public const TYPE_MOTHER = 2;

    public const MARITAL_SINGLE   = 1;
    public const MARITAL_MARRIED  = 2;
    public const MARITAL_DIVORCED = 3;
    public const MARITAL_WIDOWED  = 4;

    protected $table = 'academic_student_parents';

    protected $fillable = [
        'academic_student_id',
        'parent_type',
        'academic_guardian_id',
        'name',
        'civil_id',
        'nationality',
        'occupation',
        'workplace',
        'company_branch',
        'marital_status',
        'phone',
        'email',
    ];

    protected $casts = [
        'academic_student_id'  => 'integer',
        'parent_type'          => 'integer',
        'academic_guardian_id' => 'integer',
        'marital_status'       => 'integer',
    ];

    protected $appends = [
        'parent_type_label',
        'marital_status_label',
    ];

    //------------------------------------------
    public function student(): BelongsTo
    {
        return $this->belongsTo(AcademicStudent::class, 'academic_student_id');
    }

    //------------------------------------------
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(AcademicGuardian::class, 'academic_guardian_id');
    }

    //------------------------------------------
    public function getParentTypeLabelAttribute(): string
    {
        return match ($this->parent_type) {
            self::TYPE_FATHER => __('Father'),
            self::TYPE_MOTHER => __('Mother'),
            default           => '-',
        };
    }

    //------------------------------------------
    public function getMaritalStatusLabelAttribute(): string
    {
        return match ($this->marital_status) {
            self::MARITAL_SINGLE   => __('Single'),
            self::MARITAL_MARRIED  => __('Married'),
            self::MARITAL_DIVORCED => __('Divorced'),
            self::MARITAL_WIDOWED  => __('Widowed'),
            default                => '-',
        };
    }
}
