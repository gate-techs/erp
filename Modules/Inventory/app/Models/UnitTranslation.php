<?php

namespace Modules\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitTranslation extends Model
{
    protected $table = 'unit_translations';

    public $timestamps = false;

    protected $fillable = ['unit_id', 'locale', 'name'];

    //------------------------------------------
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }
}
