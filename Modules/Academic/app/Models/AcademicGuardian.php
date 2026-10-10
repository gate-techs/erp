<?php

namespace Modules\Academic\Models;

use App\Models\User;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;

class AcademicGuardian extends Model
{
    use HasFactory, SoftDeletes, Translatable;

    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public array $translatedAttributes = [
        'name',
    ];

    protected $table = 'academic_guardians';

    protected $fillable = [
        'guardian_code',
        'civil_id',
        'user_id',
        'institution_id',
        'branch_id',
        'phone_code',
        'phone',
        'email',
        'photo',
        'status',
        'created_by',
    ];

    protected $casts = [
        'user_id'        => 'integer',
        'institution_id' => 'integer',
        'branch_id'      => 'integer',
        'created_by'     => 'integer',
    ];

    protected $appends = [
        'photo_url',
        'full_phone',
    ];

    //------------------------------------------
    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
        ];
    }

    //------------------------------------------
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //------------------------------------------
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Nursery::class, 'institution_id');
    }

    //------------------------------------------
    /**
     * Alias for institution.
     */
    public function nursery(): BelongsTo
    {
        return $this->belongsTo(Nursery::class, 'institution_id');
    }

    //------------------------------------------
    public function branch(): BelongsTo
    {
        return $this->belongsTo(NurseryBranch::class, 'branch_id');
    }

    //------------------------------------------
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //------------------------------------------
    public function students(): HasMany
    {
        return $this->hasMany(AcademicStudent::class, 'academic_guardian_id');
    }

    //------------------------------------------
    public function parents(): HasMany
    {
        return $this->hasMany(AcademicStudentParent::class, 'academic_guardian_id');
    }

    //------------------------------------------
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
                return $this->photo;
            }

            if (Storage::disk('public')->exists($this->photo)) {
                return Storage::disk('public')->url($this->photo);
            }

            if (file_exists(public_path($this->photo))) {
                return asset($this->photo);
            }
        }

        // Fallback to linked user avatar if available
        if ($this->user && $this->user->avatar_url) {
            return $this->user->avatar_url;
        }

        return asset('assets/img/avatars/1.png');
    }

    //------------------------------------------
    public function getFullPhoneAttribute(): string
    {
        $phone = $this->phone ? trim($this->phone) : '';
        $code = $this->phone_code ? trim($this->phone_code) : '';

        if (str_starts_with($phone, '+')) {
            return $phone;
        }

        if (! empty($code) && ! empty($phone)) {
            return "{$code} {$phone}";
        }

        return $phone ?: '-';
    }

    //------------------------------------------
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    //------------------------------------------
    public function scopeByInstitution(Builder $query, int $institutionId): Builder
    {
        return $query->where('institution_id', $institutionId);
    }

    //------------------------------------------
    public function scopeByBranch(Builder $query, int $branchId): Builder
    {
        return $query->where('branch_id', $branchId);
    }
}
