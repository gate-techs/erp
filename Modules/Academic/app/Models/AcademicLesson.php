<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Modules\Nursery\Models\Nursery;

class AcademicLesson extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public array $translatedAttributes = [
        'title',
        'content',
    ];

    protected $table = 'academic_lessons';

    protected $fillable = [
        'institution_id',
        'academic_stage_id',
        'academic_subject_id',
        'start_date',
        'end_date',
        'image',
        'order',
        'status',
        'created_by',
    ];

    protected $casts = [
        'institution_id'      => 'integer',
        'academic_stage_id'   => 'integer',
        'academic_subject_id' => 'integer',
        'start_date'          => 'date',
        'end_date'            => 'date',
        'order'               => 'integer',
        'created_by'          => 'integer',
    ];

    protected $appends = [
        'image_url',
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

    public function academicStage(): BelongsTo
    {
        return $this->belongsTo(AcademicStage::class, 'academic_stage_id');
    }

    public function academicSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class, 'academic_subject_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(AcademicLessonAttachment::class, 'academic_lesson_id');
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

    public function scopeForStage(Builder $query, int $stageId): Builder
    {
        return $query->where('academic_stage_id', $stageId);
    }

    public function scopeForSubject(Builder $query, int $subjectId): Builder
    {
        return $query->where('academic_subject_id', $subjectId);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order', 'asc')->orderBy('id', 'asc');
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
}
