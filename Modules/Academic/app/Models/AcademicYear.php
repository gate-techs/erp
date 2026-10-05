<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    public array $translatedAttributes = [
        'name',
    ];

    /**
     * The table associated with the model.
     */
    protected $table = 'academic_years';

    /**
     * Attributes that can be mass assigned.
     */
    protected $fillable = [
        'institution_id',
        'code',
        'start_date',
        'end_date',
        'status',
        'is_current',
        'notes',
        'created_by',
    ];

    /**
     * Attribute casting definitions.
     */
    protected $casts = [
        'institution_id' => 'integer',
        'start_date'    => 'date',
        'end_date'      => 'date',
        'is_current'    => 'boolean',
    ];

    /**
     * Supported lifecycle statuses.
     */
    public const STATUS_DRAFT = 'draft';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_ARCHIVED = 'archived';

    /**
     * Return all supported statuses.
     *
     * @return array<int, string>
     */
    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_ACTIVE,
            self::STATUS_CLOSED,
            self::STATUS_ARCHIVED,
        ];
    }

    /**
     * The user who created the academic year.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to active academic years.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope to the current academic year.
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }

    /**
     * Determine whether the year is active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }
}
