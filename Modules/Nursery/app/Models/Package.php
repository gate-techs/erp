<?php

namespace Modules\Nursery\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    protected $table = 'packages';

    public array $translatedAttributes = [
        'name',
        'description',
    ];

    protected $fillable = [
        'nursery_id',
        'cover_image',
        'price',
        'discount_type',
        'discount_value',
        'sort_order',
        'status',
        'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:3',
        'discount_value' => 'decimal:3',
        'sort_order' => 'integer',
    ];

    public function nursery(): BelongsTo
    {
        return $this->belongsTo(Nursery::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function features(): HasMany
    {
        return $this->hasMany(PackageFeature::class)->orderBy('sort_order');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PackageImage::class)->orderBy('sort_order');
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if (! $this->cover_image) {
            return null;
        }

        return filter_var($this->cover_image, FILTER_VALIDATE_URL)
            ? $this->cover_image
            : asset('storage/' . $this->cover_image);
    }
}
