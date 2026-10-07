<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'user_type',
        'is_active',
        'avatar',
        'locale',
        'last_login_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    //==================================
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'user_type' => UserType::class,
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Relationship: Linked employee profile if user is an employee.
     */
    public function employee(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\Modules\HR\Models\Employee::class, 'user_id');
    }

    /**
     * Browsers/devices registered for Web Push delivery.
     */
    public function pushSubscriptions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\Modules\Settings\Models\PushSubscription::class, 'user_id');
    }

    /**
     * Accessor: Avatar URL with fallback to employee avatar or gender-aware default.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->employee && $this->employee->avatar_url) {
            return $this->employee->avatar_url;
        }

        if (!empty($this->avatar)) {
            if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
                return $this->avatar;
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($this->avatar)) {
                return \Illuminate\Support\Facades\Storage::disk('public')->url($this->avatar);
            }
            if (file_exists(public_path($this->avatar))) {
                return asset($this->avatar);
            }
            if (file_exists(public_path('storage/' . $this->avatar))) {
                return asset('storage/' . $this->avatar);
            }
        }

        // Gender-aware avatar fallback
        $gender = strtolower(trim((string) ($this->employee?->gender ?? 'male')));
        $isFemale = in_array($gender, ['female', 'f', 'أنثى', 'انثى']);

        if ($isFemale) {
            if (file_exists(public_path('assets/portal/female-avatar.png'))) {
                return asset('assets/portal/female-avatar.png');
            }
            if (file_exists(public_path('assets/portal/teacher-avatar.png'))) {
                return asset('assets/portal/teacher-avatar.png');
            }
            return asset('assets/img/avatars/female-avatar.png');
        }

        if (file_exists(public_path('assets/portal/male-avatar.png'))) {
            return asset('assets/portal/male-avatar.png');
        }
        if (file_exists(public_path('assets/img/avatars/male-avatar.png'))) {
            return asset('assets/img/avatars/male-avatar.png');
        }

        return asset('assets/portal/header-emblem.png');
    }

    /**
     * Check if user is a developer.
     */
    public function isDeveloper(): bool
    {
        return $this->user_type === \App\Enums\UserType::DEVELOPER || $this->user_type?->value === 'developer';
    }
}
