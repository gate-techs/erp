<?php

namespace Modules\HR\Models;

use App\Models\User;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Enums\DepartmentStatus;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;

class Department extends Model implements TranslatableContract
{
    use HasFactory, SoftDeletes, Translatable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'departments';

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
        'code',
        'nursery_id',
        'branch_id',
        'parent_id',
        'manager_id',
        'status',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => DepartmentStatus::class,
    ];

    //======================
    /**
     * Get parent department (الادارة التابع لها).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    //======================
    /**
     * Get child departments (الاقسام الفرعية).
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    //======================
    /**
     * Get employees belonging to this department.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'department_id');
    }

    //======================
    /**
     * Get the nursery associated with this department.
     */
    public function nursery(): BelongsTo
    {
        return $this->belongsTo(Nursery::class, 'nursery_id');
    }

    //======================
    /**
     * Get the branch associated with this department (if branch-specific).
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
    }

    //======================
    /**
     * Get the manager of this department.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    //======================
    /**
     * Get the user who created this department.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //======================
    /**
     * Scope for main administrations only (parent_id is null).
     */
    public function scopeMainDepartments(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }

    //======================
    /**
     * Scope for sub-departments only (parent_id is not null).
     */
    public function scopeSubDepartments(Builder $query): Builder
    {
        return $query->whereNotNull('parent_id');
    }

    //======================
    /**
     * Scope for active departments.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', DepartmentStatus::ACTIVE);
    }
}
