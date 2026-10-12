<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Nursery\Models\Nursery;

class Calender extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    /**
     * Attributes that are translatable via calender_translations.
     */
    public array $translatedAttributes = [
        'title',
        'description',
    ];

    public string $translationModel = CalenderTranslation::class;

    protected $table = 'calenders';

    protected $fillable = [
        'institution_id',
        'start_date',
        'end_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'institution_id' => 'integer',
        'start_date'     => 'date:Y-m-d',
        'end_date'       => 'date:Y-m-d',
        'created_by'     => 'integer',
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

    /**
     * Alias for 'title' translation so $calender->name works interchangeably with $calender->title.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->title,
            set: fn ($value) => ['title' => $value],
        );
    }

    //---------------------------------------------------------
    // Relationships
    //---------------------------------------------------------

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Nursery::class, 'institution_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //---------------------------------------------------------
    // Query Scopes
    //---------------------------------------------------------

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeForInstitution(Builder $query, int $institutionId): Builder
    {
        return $query->where('institution_id', $institutionId);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_date', '>=', now()->toDateString())
            ->orderBy('start_date', 'asc');
    }
}
