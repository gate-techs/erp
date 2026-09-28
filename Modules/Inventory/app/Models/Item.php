<?php

namespace Modules\Inventory\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    protected $table = 'items';

    public array $translatedAttributes = ['name', 'description'];

    protected $fillable = [
        'category_id',
        'unit_id',
        'code',
        'barcode',
        'image_path',
        'cost_price',
        'selling_price',
        'status',
        'created_by',
    ];

    protected $casts = [
        'category_id'   => 'integer',
        'unit_id'       => 'integer',
        'cost_price'    => 'decimal:4',
        'selling_price' => 'decimal:4',
        'created_by'    => 'integer',
    ];

    //------------------------------------------
    public function translations(): HasMany
    {
        return $this->hasMany(ItemTranslation::class, 'item_id');
    }

    //------------------------------------------
    public function category(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'category_id');
    }

    //------------------------------------------
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    //------------------------------------------
    public function stock(): HasMany
    {
        return $this->hasMany(ItemStock::class, 'item_id');
    }

    //------------------------------------------
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //------------------------------------------
    public function getTotalQuantityAttribute(): float
    {
        return (float) $this->stock()->sum('quantity');
    }
}
