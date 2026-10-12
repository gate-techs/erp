<?php

namespace Modules\Settings\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Settings\Enums\GeneralSettingType;

class GeneralSetting extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'general_settings';

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public array $translatedAttributes = [
        'name',
        'description',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'parent_id',
        'value',
        'image',
        'status',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => GeneralSettingType::class,
        'status' => 'boolean',
    ];

    /**
     * Get the parent setting (for hierarchy, e.g. City belongs to Government).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get children settings (e.g. Cities under a Government).
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Get the user who created this setting record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Scope query to filter by type.
     */
    public function scopeOfType(Builder $query, GeneralSettingType|string $type): Builder
    {
        $typeValue = $type instanceof GeneralSettingType ? $type->value : $type;

        return $query->where('type', $typeValue);
    }

    /**
     * Scope query to only include root parents (parent_id IS NULL).
     */
    public function scopeParentsOnly(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
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
}
