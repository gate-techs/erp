<?php

namespace Modules\HR\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\HrSettingStatus;
use Modules\HR\Enums\HrSettingType;

class HrSetting extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'hr_settings';

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public array $translatedAttributes = [
        'name',
        'description',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'parent_id',
        'status',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type'   => HrSettingType::class,
        'status' => HrSettingStatus::class,
    ];

    //======================
    /**
     * Get the parent setting.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    //======================
    /**
     * Get the child settings.
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    //======================
    /**
     * User who created this record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //======================
    /**
     * Scope query by setting type.
     */
    public function scopeOfType($query, string|HrSettingType $type)
    {
        $val = $type instanceof HrSettingType ? $type->value : $type;

        return $query->where('type', $val);
    }

    //======================
    /**
     * Scope query by active status.
     */
    public function scopeActive($query)
    {
        return $query->where('status', HrSettingStatus::ACTIVE->value);
    }
}
