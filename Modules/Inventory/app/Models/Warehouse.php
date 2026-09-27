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
use Modules\Nursery\Models\NurseryBranch;
use Modules\Accounting\Models\Entity;

class Warehouse extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    protected $table = 'warehouses';

    public array $translatedAttributes = [
        'name',
        'description',
    ];

    protected $fillable = [
        'entity_id',
        'branch_id',
        'code',
        'type',
        'status',
        'created_by',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'branch_id' => 'integer',
        'created_by' => 'integer',
    ];

    //------------------------------------------
    public function translations(): HasMany
    {
        return $this->hasMany(WarehouseTranslation::class, 'warehouse_id');
    }

    //------------------------------------------
    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
    }

    //------------------------------------------
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    //------------------------------------------
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //------------------------------------------
    public function isMain(): bool
    {
        return $this->type === 'main';
    }

    //------------------------------------------
    public function isBranch(): bool
    {
        return $this->type === 'branch';
    }
}
