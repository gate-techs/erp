<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;
use Modules\Settings\Models\GeneralSetting;

class Event extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    protected $table = 'events';

    public array $translatedAttributes = [
        'name',
        'description',
    ];

    public string $translationModel = EventTranslation::class;

    protected $fillable = [
        'institution_id',
        'branch_id',
        'academic_class_id',
        'event_type_id',
        'image',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'price',
        'status',
        'created_by',
    ];

    protected $casts = [
        'institution_id'    => 'integer',
        'branch_id'         => 'integer',
        'academic_class_id' => 'integer',
        'event_type_id'     => 'integer',
        'start_date'        => 'date:Y-m-d',
        'end_date'          => 'date:Y-m-d',
        'price'             => 'decimal:3',
        'created_by'        => 'integer',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_UPCOMING = 'upcoming';
    public const STATUS_COMPLETED = 'completed';

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE    => __('Active'),
            self::STATUS_INACTIVE  => __('Inactive'),
            self::STATUS_UPCOMING  => __('Upcoming'),
            self::STATUS_COMPLETED => __('Completed'),
        ];
    }

    /**
     * Relationship: Nursery / Institution
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Nursery::class, 'institution_id');
    }

    /**
     * Relationship: Nursery Branch
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
    }

    /**
     * Relationship: Academic Class (الفصل الدراسي)
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(AcademicClass::class, 'academic_class_id');
    }

    /**
     * Relationship: Event Type (نوع الفاعلية)
     */
    public function eventType(): BelongsTo
    {
        return $this->belongsTo(GeneralSetting::class, 'event_type_id');
    }

    /**
     * Relationship: Creator User
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get image full URL.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    /**
     * Scopes
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeForInstitution(Builder $query, int $institutionId): Builder
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeForBranch(Builder $query, int $branchId): Builder
    {
        return $query->where('branch_id', $branchId);
    }
}
