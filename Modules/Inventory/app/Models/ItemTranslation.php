<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemTranslation extends Model
{
    protected $table = 'item_translations';

    public $timestamps = false;

    protected $fillable = ['item_id', 'locale', 'name', 'description'];

    //------------------------------------------
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
