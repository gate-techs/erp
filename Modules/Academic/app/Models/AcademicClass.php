<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicClass extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    /**
     * Translation attributes stored in academic_class_translations.
     */
    public array $translatedAttributes = [
        'name',
        'description',
    ];

    protected $table = 'academic_classes';

    protected $fillable = [
        'institution_id',
        'branch_id',
        'academic_year_id',
        'academic_stage_id',
        'capacity',
        'color',
        'image',
        'status',
        'created_by',
    ];

    protected $casts = [
        'institution_id' => 'integer',
        'branch_id' => 'integer',
        'academic_year_id' => 'integer',
        'academic_stage_id' => 'integer',
        'capacity' => 'integer',
    ];

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
        ];
    }

    protected $appends = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'image_url',
    ];

    public function getImageUrlAttribute(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        if (str_starts_with($this->image, 'assets/')) {
            return asset($this->image);
        }

        return asset('storage/' . $this->image);
    }

    public function getNameArAttribute(): ?string
    {
        return $this->translate('ar')?->name;
    }

    public function getNameEnAttribute(): ?string
    {
        return $this->translate('en')?->name;
    }

    public function getDescriptionArAttribute(): ?string
    {
        return $this->translate('ar')?->description;
    }

    public function getDescriptionEnAttribute(): ?string
    {
        return $this->translate('en')?->description;
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(\Modules\Nursery\Models\Nursery::class, 'institution_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\Nursery\Models\NurseryBranch::class, 'branch_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function academicStage(): BelongsTo
    {
        return $this->belongsTo(AcademicStage::class, 'academic_stage_id');
    }

    public function stage(): BelongsTo
    {
        return $this->academicStage();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function students(): HasMany
    {
        return $this->hasMany(AcademicStudent::class, 'academic_class_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(AcademicStudentAttendance::class, 'academic_class_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
