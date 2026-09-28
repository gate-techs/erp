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

class Unit extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    protected $table = 'units';

    public array $translatedAttributes = ['name'];

    protected $fillable = ['symbol', 'status', 'created_by'];

    protected $casts = ['created_by' => 'integer'];

    //------------------------------------------
    public function translations(): HasMany
    {
        return $this->hasMany(UnitTranslation::class, 'unit_id');
    }

    //------------------------------------------
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'unit_id');
    }

    //------------------------------------------
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
