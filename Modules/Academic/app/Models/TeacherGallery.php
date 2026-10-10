<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Models\Employee;

class TeacherGallery extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'teacher_galleries';

    protected $fillable = [
        'teacher_id',
        'class_room_id',
        'student_id',
        'title',
        'description',
        'date',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    //------------------------------------------
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'teacher_id');
    }

    //------------------------------------------
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'class_room_id');
    }

    //------------------------------------------
    public function student(): BelongsTo
    {
        return $this->belongsTo(AcademicStudent::class, 'student_id');
    }

    //------------------------------------------
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            AcademicStudent::class,
            'teacher_gallery_students',
            'teacher_gallery_id',
            'student_id'
        )->withTimestamps();
    }

    //------------------------------------------
    public function images(): HasMany
    {
        return $this->hasMany(TeacherGalleryImage::class, 'teacher_gallery_id');
    }

    //------------------------------------------
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //------------------------------------------
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    //------------------------------------------
    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    //------------------------------------------
    public function getSharingScopeAttribute(): string
    {
        $studentsCount = $this->students()->count();

        if ($studentsCount > 1) {
            return 'selected'; // مجموعة طلاب مختارين
        }

        if ($this->student_id || $studentsCount === 1) {
            return 'single'; // طالب واحد
        }

        return 'class'; // فصل بالكامل
    }

    //------------------------------------------
    public function scopeForStudent(Builder $query, int $studentId): Builder
    {
        $student = AcademicStudent::find($studentId);

        return $query->where(function (Builder $q) use ($studentId, $student) {
            $q->where('student_id', $studentId)
                ->orWhereHas('students', function (Builder $sq) use ($studentId) {
                    $sq->where('academic_students.id', $studentId);
                });

            if ($student && $student->academic_class_id) {
                $q->orWhere(function (Builder $cq) use ($student) {
                    $cq->whereNull('student_id')
                        ->whereDoesntHave('students')
                        ->where('class_room_id', $student->academic_class_id);
                });
            }
        });
    }
}
