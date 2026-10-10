<?php

namespace Modules\Nursery\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageFeatureTranslation extends Model
{
    use HasFactory;

    protected $table = 'package_feature_translations';
    public $timestamps = false;

    protected $fillable = [
        'package_feature_id',
        'locale',
        'name',
    ];

    public function feature(): BelongsTo
    {
        return $this->belongsTo(PackageFeature::class, 'package_feature_id');
    }
}
