<?php

namespace Modules\Accounting\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Accounting\Enums\CostCenterStatus;
use Modules\Accounting\Enums\CostCenterType;
use Modules\Accounting\Traits\AccountingAuditable;
use Modules\Nursery\Models\NurseryBranch;

class CostCenter extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable, AccountingAuditable;

    protected $table = 'accounting_cost_centers';

    public array $translatedAttributes = ['name', 'description'];

    protected $fillable = [
        'entity_id',
        'parent_id',
        'branch_id',
        'code',
        'type',
        'status',
        'created_by',
    ];

    protected $casts = [
        'entity_id' => 'integer',
        'parent_id' => 'integer',
        'branch_id' => 'integer',
        'created_by' => 'integer',
        'type' => CostCenterType::class,
        'status' => CostCenterStatus::class,
    ];

    //===============================
    public function entity(): BelongsTo
    {
        return $this->belongsTo(Entity::class, 'entity_id');
    }

    //===============================
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    //===============================
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    //===============================
    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
    }

    //===============================
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //===============================
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', CostCenterStatus::ACTIVE->value);
    }

    //===============================
    public function scopeForEntity(Builder $query, int $entityId): Builder
    {
        return $query->where('entity_id', $entityId);
    }

    //===============================
    public function scopeRoots(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    //===============================
    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    //===============================
    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }
}
