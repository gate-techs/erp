<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Modules\Nursery\Models\Nursery;

class AcademicSubject extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public array $translatedAttributes = [
        'name',
        'description',
    ];

    protected $table = 'academic_subjects';

    protected $fillable = [
        'institution_id',
        'academic_year_id',
        'image',
        'status',
        'created_by',
    ];

    protected $casts = [
        'institution_id' => 'integer',
        'academic_year_id' => 'integer',
        'created_by' => 'integer',
    ];

    protected $appends = [
        'image_url',
        'stage_ids',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
        ];
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Nursery::class, 'institution_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function academicStages(): BelongsToMany
    {
        return $this->belongsToMany(
            AcademicStage::class,
            'academic_subject_stage',
            'academic_subject_id',
            'academic_stage_id'
        )->withTimestamps();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(AcademicSubjectAttachment::class, 'academic_subject_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeForInstitution(Builder $query, int $institutionId): Builder
    {
        return $query->where('institution_id', $institutionId);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (empty($this->image)) {
            return null;
        }

        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }

        if (str_starts_with($this->image, 'assets/')) {
            return asset($this->image);
        }

        return Storage::disk('public')->exists($this->image)
            ? asset('storage/' . $this->image)
            : asset($this->image);
    }

    public function getStageIdsAttribute(): array
    {
        if ($this->relationLoaded('academicStages')) {
            return $this->academicStages->pluck('id')->toArray();
        }

        return $this->academicStages()->pluck('academic_stages.id')->toArray();
    }
}
