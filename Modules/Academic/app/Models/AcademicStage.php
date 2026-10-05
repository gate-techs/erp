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

class AcademicStage extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    /**
     * Translation attributes stored in academic_stage_translations.
     */
    public array $translatedAttributes = [
        'name',
        'description',
    ];

    /**
     * The table associated with the model.
     */
    protected $table = 'academic_stages';

    /**
     * Attributes that can be mass assigned.
     */
    protected $fillable = [
        'institution_id',
        'code',
        'min_age_months',
        'max_age_months',
        'status',
        'created_by',
    ];

    /**
     * Attribute casting definitions.
     */
    protected $casts = [
        'institution_id' => 'integer',
        'min_age_months' => 'integer',
        'max_age_months' => 'integer',
    ];

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    /**
     * Return all supported lifecycle statuses.
     *
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
        ];
    }

    /**
     * The user who created the stage.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to active stages.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Determine whether the stage is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
