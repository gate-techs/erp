<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseTranslation extends Model
{
    protected $table = 'warehouse_translations';

    public $timestamps = false;

    protected $fillable = [
        'warehouse_id',
        'locale',
        'name',
        'description',
    ];

    //------------------------------------------
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
