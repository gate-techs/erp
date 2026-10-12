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

class ItemCategory extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    protected $table = 'item_categories';

    public array $translatedAttributes = ['name', 'description'];

    protected $fillable = ['parent_id', 'code', 'sort_order', 'status', 'created_by'];

    protected $casts = ['parent_id' => 'integer', 'sort_order' => 'integer', 'created_by' => 'integer'];

    //------------------------------------------
    public function translations(): HasMany
    {
        return $this->hasMany(ItemCategoryTranslation::class, 'item_category_id');
    }

    //------------------------------------------
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    //------------------------------------------
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    //------------------------------------------
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //------------------------------------------
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'category_id');
    }
}
