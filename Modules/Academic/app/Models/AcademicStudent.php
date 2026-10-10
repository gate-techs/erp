<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Astrotomic\Translatable\Translatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Settings\Models\GeneralSetting;

class AcademicStudent extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public const STATUS_ACTIVE     = 'active';
    public const STATUS_PENDING    = 'pending';
    public const STATUS_INACTIVE   = 'inactive';
    public const STATUS_GRADUATED  = 'graduated';
    public const STATUS_WITHDRAWN  = 'withdrawn';

    public const GENDER_MALE   = 1;
    public const GENDER_FEMALE = 2;

    public array $translatedAttributes = [
        'name',
    ];

    protected $table = 'academic_students';

    protected $fillable = [
        'student_code',
        'civil_id',
        'institution_id',
        'branch_id',
        'academic_year_id',
        'academic_stage_id',
        'academic_class_id',
        'nationality_id',
        'academic_guardian_id',
        'birth_date',
        'gender',
        'photo',
        'status',
        'terms_accepted',
        'terms_accepted_at',
        'financial_policy_accepted',
        'financial_policy_accepted_at',
        'pickup_policy_accepted',
        'pickup_policy_accepted_at',
        'photo_consent',
        'photo_consent_notes',
        'medical_declaration_accepted',
        'medical_declaration_accepted_at',
        'guardian_notes',
        'admin_notes',
        'created_by',
    ];

    protected $casts = [
        'institution_id'                  => 'integer',
        'branch_id'                       => 'integer',
        'academic_year_id'                => 'integer',
        'academic_stage_id'               => 'integer',
        'academic_class_id'               => 'integer',
        'nationality_id'                  => 'integer',
        'academic_guardian_id'            => 'integer',
        'gender'                          => 'integer',
        'birth_date'                      => 'date',
        'terms_accepted'                  => 'boolean',
        'terms_accepted_at'               => 'datetime',
        'financial_policy_accepted'       => 'boolean',
        'financial_policy_accepted_at'    => 'datetime',
        'pickup_policy_accepted'          => 'boolean',
        'pickup_policy_accepted_at'       => 'datetime',
        'medical_declaration_accepted'    => 'boolean',
        'medical_declaration_accepted_at' => 'datetime',
        'photo_consent'                   => 'boolean',
        'created_by'                      => 'integer',
    ];

    protected $appends = [
        'photo_url',
        'gender_label',
        'age',
    ];

    //------------------------------------------
    /**
     * Get valid statuses.
     *
     * @return array<string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_PENDING,
            self::STATUS_INACTIVE,
            self::STATUS_GRADUATED,
            self::STATUS_WITHDRAWN,
        ];
    }

    //------------------------------------------
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Nursery::class, 'institution_id');
    }

    //------------------------------------------
    /**
     * Alias for institution.
     */
    public function nursery(): BelongsTo
    {
        return $this->belongsTo(Nursery::class, 'institution_id');
    }

    //------------------------------------------
    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
    }

    //------------------------------------------
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    //------------------------------------------
    public function stage(): BelongsTo
    {
        return $this->belongsTo(AcademicStage::class, 'academic_stage_id');
    }

    //------------------------------------------
    public function class(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'academic_class_id');
    }

    //------------------------------------------
    public function nationality(): BelongsTo
    {
        return $this->belongsTo(GeneralSetting::class, 'nationality_id');
    }

    //------------------------------------------
    public function guardian(): BelongsTo
    {
        return $this->belongsTo(AcademicGuardian::class, 'academic_guardian_id');
    }

    public function studentFees(): HasMany
    {
        return $this->hasMany(AcademicStudentFee::class, 'academic_student_id');
    }

    //------------------------------------------
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //------------------------------------------
    public function parents(): HasMany
    {
        return $this->hasMany(AcademicStudentParent::class, 'academic_student_id');
    }

    //------------------------------------------
    public function father(): HasOne
    {
        return $this->hasOne(AcademicStudentParent::class, 'academic_student_id')
            ->where('parent_type', AcademicStudentParent::TYPE_FATHER);
    }

    //------------------------------------------
    public function mother(): HasOne
    {
        return $this->hasOne(AcademicStudentParent::class, 'academic_student_id')
            ->where('parent_type', AcademicStudentParent::TYPE_MOTHER);
    }

    //------------------------------------------
    public function siblings(): HasMany
    {
        return $this->hasMany(AcademicStudentSibling::class, 'academic_student_id');
    }

    //------------------------------------------
    public function diseases(): HasMany
    {
        return $this->hasMany(AcademicStudentDisease::class, 'academic_student_id');
    }

    //------------------------------------------
    public function medicalResponses(): HasMany
    {
        return $this->hasMany(AcademicStudentMedicalResponse::class, 'academic_student_id');
    }

    //------------------------------------------
    public function emergencyContacts(): HasMany
    {
        return $this->hasMany(AcademicStudentEmergencyContact::class, 'academic_student_id')
            ->orderBy('priority', 'asc');
    }

    //------------------------------------------
    public function authorizedPickups(): HasMany
    {
        return $this->hasMany(AcademicStudentAuthorizedPickup::class, 'academic_student_id');
    }

    //------------------------------------------
    public function informationResponses(): HasMany
    {
        return $this->hasMany(AcademicStudentInformationResponse::class, 'academic_student_id')
            ->orderBy('question_code', 'asc');
    }

    //------------------------------------------
    public function attendances(): HasMany
    {
        return $this->hasMany(AcademicStudentAttendance::class, 'academic_student_id');
    }

    //------------------------------------------
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
                return $this->photo;
            }

            if (Storage::disk('public')->exists($this->photo)) {
                return Storage::disk('public')->url($this->photo);
            }

            if (file_exists(public_path($this->photo))) {
                return asset($this->photo);
            }
        }

        return $this->gender === self::GENDER_FEMALE
            ? asset('assets/img/avatars/2.png')
            : asset('assets/img/avatars/1.png');
    }

    //------------------------------------------
    public function getGenderLabelAttribute(): string
    {
        return match ($this->gender) {
            self::GENDER_MALE   => __('Male'),
            self::GENDER_FEMALE => __('Female'),
            default             => '-',
        };
    }

    //------------------------------------------
    public function getAgeAttribute(): ?string
    {
        if (! $this->birth_date) {
            return null;
        }

        $years = Carbon::now()->diffInYears($this->birth_date);
        $months = Carbon::now()->diffInMonths($this->birth_date) % 12;

        if ($years > 0) {
            return "{$years} " . __('years') . ($months > 0 ? " {$months} " . __('months') : '');
        }

        return "{$months} " . __('months');
    }

    //------------------------------------------
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    //------------------------------------------
    public function scopeByInstitution(Builder $query, int $institutionId): Builder
    {
        return $query->where('institution_id', $institutionId);
    }

    //------------------------------------------
    public function scopeByBranch(Builder $query, int $branchId): Builder
    {
        return $query->where('branch_id', $branchId);
    }

    //------------------------------------------
    public function scopeByYear(Builder $query, int $yearId): Builder
    {
        return $query->where('academic_year_id', $yearId);
    }

    //------------------------------------------
    public function scopeByClass(Builder $query, int $classId): Builder
    {
        return $query->where('academic_class_id', $classId);
    }

    //------------------------------------------
    public function teacherGalleries(): BelongsToMany
    {
        return $this->belongsToMany(
            TeacherGallery::class,
            'teacher_gallery_students',
            'student_id',
            'teacher_gallery_id'
        )->withTimestamps();
    }
}
