<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemStock extends Model
{
    protected $table = 'item_stock';

    protected $fillable = ['item_id', 'warehouse_id', 'quantity'];

    protected $casts = [
        'item_id'      => 'integer',
        'warehouse_id' => 'integer',
        'quantity'     => 'decimal:4',
    ];

    //------------------------------------------
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    //------------------------------------------
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
