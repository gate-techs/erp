<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemCategoryTranslation extends Model
{
    protected $table = 'item_category_translations';

    public $timestamps = false;

    protected $fillable = ['item_category_id', 'locale', 'name', 'description'];

    //------------------------------------------
    public function itemCategory(): BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'item_category_id');
    }
}
