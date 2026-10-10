<?php

namespace Modules\Nursery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageTranslation extends Model
{
    use HasFactory;

    protected $table = 'package_translations';
    public $timestamps = false;

    protected $fillable = [
        'package_id',
        'locale',
        'name',
        'description',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
