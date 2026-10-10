<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;

class AcademicStudentAttendance extends Model
{
    use HasFactory;

    public const STATUS_ABSENT  = 0;
    public const STATUS_ATTEND  = 1;
    public const STATUS_LATE    = 2;
    public const STATUS_EXCUSED = 3;

    protected $table = 'academic_student_attendances';

    protected $fillable = [
        'academic_student_id',
        'academic_class_id',
        'institution_id',
        'branch_id',
        'date',
        'status',
        'approved',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'academic_student_id' => 'integer',
        'academic_class_id'   => 'integer',
        'institution_id'      => 'integer',
        'branch_id'           => 'integer',
        'date'                => 'date:Y-m-d',
        'status'              => 'integer',
        'approved'            => 'boolean',
        'approved_at'         => 'datetime',
        'approved_by'         => 'integer',
        'recorded_by'         => 'integer',
    ];

    protected $appends = [
        'status_label',
        'is_present',
        'student_id',
        'classroom_id',
    ];

    //------------------------------------------
    // Relationships
    //------------------------------------------

    //------------------------------------------
    public function student(): BelongsTo
    {
        return $this->belongsTo(AcademicStudent::class, 'academic_student_id');
    }

    //------------------------------------------
    public function class(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'academic_class_id');
    }

    //------------------------------------------
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Nursery::class, 'institution_id');
    }

    //------------------------------------------
    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
    }

    //------------------------------------------
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    //------------------------------------------
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    //------------------------------------------
    // Accessors & Mutators
    //------------------------------------------

    //------------------------------------------
    public function getStudentIdAttribute(): ?int
    {
        return $this->academic_student_id;
    }

    //------------------------------------------
    public function getClassroomIdAttribute(): ?int
    {
        return $this->academic_class_id;
    }

    //------------------------------------------
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_ATTEND  => __('Present'),
            self::STATUS_ABSENT  => __('Absent'),
            self::STATUS_LATE    => __('Late'),
            self::STATUS_EXCUSED => __('Excused'),
            default              => __('Unknown'),
        };
    }

    //------------------------------------------
    public function getIsPresentAttribute(): bool
    {
        return $this->status === self::STATUS_ATTEND || $this->status === self::STATUS_LATE;
    }

    //------------------------------------------
    // Scopes
    //------------------------------------------

    //------------------------------------------
    public function scopeForDate(Builder $query, string $date): Builder
    {
        return $query->whereDate('date', $date);
    }

    //------------------------------------------
    public function scopeDateRange(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    //------------------------------------------
    public function scopeForClass(Builder $query, int $classId): Builder
    {
        return $query->where('academic_class_id', $classId);
    }

    //------------------------------------------
    public function scopeForStudent(Builder $query, int $studentId): Builder
    {
        return $query->where('academic_student_id', $studentId);
    }

    //------------------------------------------
    public function scopeApproved(Builder $query, bool $approved = true): Builder
    {
        return $query->where('approved', $approved);
    }

    //------------------------------------------
    public function scopePresent(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_ATTEND, self::STATUS_LATE]);
    }

    //------------------------------------------
    public function scopeAbsent(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ABSENT);
    }
}
