<?php

namespace Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCenterTranslation extends Model
{
    use HasFactory;

    protected $table = 'accounting_cost_center_translations';

    public $timestamps = false;

    protected $fillable = [
        'cost_center_id',
        'locale',
        'name',
        'description',
    ];
}
