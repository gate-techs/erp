<?php

namespace Modules\Academic\Models;

use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;

class AcademicFeeType extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public array $translatedAttributes = [
        'name',
        'description',
    ];

    protected $table = 'academic_fee_types';

    protected $fillable = [
        'institution_id',
        'branch_id',
        'code',
        'status',
        'order',
        'created_by',
    ];

    protected $casts = [
        'institution_id' => 'integer',
        'branch_id'      => 'integer',
        'order'          => 'integer',
        'created_by'     => 'integer',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Nursery::class, 'institution_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
    }

    public function fees(): HasMany
    {
        return $this->hasMany(AcademicFee::class, 'academic_fee_type_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
