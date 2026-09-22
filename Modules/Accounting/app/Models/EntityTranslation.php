<?php

namespace Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityTranslation extends Model
{
    use HasFactory;

    protected $table = 'entity_translations';

    public $timestamps = false;

    protected $fillable = ['entity_id', 'locale', 'name', 'description'];
}
