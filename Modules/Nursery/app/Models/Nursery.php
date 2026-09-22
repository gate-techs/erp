<?php

namespace Modules\Nursery\Models;

use App\Enums\UserType;
use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Accounting\Models\Entity;
use Modules\Nursery\Enums\NurseryStatus;
use Modules\Settings\Models\GeneralSetting;

class Nursery extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nurseries';

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
        'government_id',
        'employee_id',
        'image',
        'lat',
        'lng',
        'whatsapp',
        'email',
        'status',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => NurseryStatus::class,
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
    ];

    //======================
    /**
     * Get government / governorate setting.
     */
    public function government(): BelongsTo
    {
        return $this->belongsTo(GeneralSetting::class, 'government_id');
    }

    //======================
    /**
     * Get responsible employee manager.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id')->where('user_type', UserType::EMPLOYEE);
    }

    //======================
    /**
     * Alias for employee / manager.
     */
    public function manager(): BelongsTo
    {
        return $this->employee();
    }

    //======================
    /**
     * Get weekly working days schedule.
     */
    public function workDays(): HasMany
    {
        return $this->hasMany(NurseryWorkDay::class, 'nursery_id');
    }

    //======================
    /**
     * Get user who created this nursery record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //======================
    /**
     * Scope query to active nurseries only.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', NurseryStatus::ACTIVE->value);
    }

    //======================
    /**
     * Scope query by government.
     */
    public function scopeInGovernment(Builder $query, int $governmentId): Builder
    {
        return $query->where('government_id', $governmentId);
    }

    //======================
    /**
     * Scope query by responsible employee.
     */
    public function scopeManagedBy(Builder $query, int $employeeId): Builder
    {
        return $query->where('employee_id', $employeeId);
    }

    //======================
    /**
     * Get image full URL or default placeholder.
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }

        if (filter_var($this->image, FILTER_VALIDATE_URL)) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    //======================
    /**
     * Check if nursery is active.
     */
    public function isActive(): bool
    {
        return $this->status === NurseryStatus::ACTIVE;
    }

    //======================
    /**
     * Get branches for this nursery.
     */
    public function branches(): HasMany
    {
        return $this->hasMany(NurseryBranch::class, 'nursery_id');
    }

    //======================
    /**
     * Get the accounting entity linked to this nursery.
     */
    //--------------------------------------------------
    public function entity(): HasOne
    {
        return $this->hasOne(Entity::class, 'nursery_id');
    }
}
