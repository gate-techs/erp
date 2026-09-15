<?php

namespace Modules\Settings\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Setting extends Model implements TranslatableContract
{
    use HasFactory, Translatable;

    /**
     * Cache key for singleton settings instance.
     */
    public const CACHE_KEY = 'app_settings_instance';

    /**
     * The attributes that are translatable.
     *
     * @var array<int, string>
     */
    public array $translatedAttributes = [
        'site_name',
        'slogan',
        'address',
        'footer_text',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        // Contact Information
        'email',
        'phone',
        'whatsapp',
        'emergency_phone',

        // Branding
        'logo',
        'favicon',
        'primary_color',

        // Regional & Financial
        'currency',
        'currency_symbol',
        'timezone',
        'default_locale',
        'tax_number',

        // Social Media
        'facebook',
        'instagram',
        'twitter',
        'tiktok',
        'youtube',
    ];

    /**
     * The "booted" method of the model.
     * Clears cached settings whenever settings are saved or deleted.
     */
    protected static function booted(): void
    {
        static::saved(function () {
            Cache::forget(self::CACHE_KEY);
        });

        static::deleted(function () {
            Cache::forget(self::CACHE_KEY);
        });
    }

    /**
     * Retrieve the cached singleton Setting instance or create default one.
     */
    public static function instance(): self
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return self::firstOrCreate(
                [],
                [
                    'currency' => 'KWD',
                    'currency_symbol' => 'د.ك',
                    'timezone' => 'Asia/Kuwait',
                    'default_locale' => 'ar',
                    'ar' => [
                        'site_name' => 'أكاديمية أسترو ليدرز',
                        'slogan' => 'نصنع قادة الغد',
                        'address' => 'الكويت',
                    ],
                    'en' => [
                        'site_name' => 'Astro Leaders Academy',
                        'slogan' => 'Shaping Tomorrow\'s Leaders',
                        'address' => 'Kuwait',
                    ],
                ]
            );
        });
    }

    /**
     * Accessor for full logo URL with fallback to default project logo.
     */
    public function getLogoUrlAttribute(): string
    {
        if (!empty($this->logo)) {
            return asset('storage/' . $this->logo);
        }

        return asset('assets/img/illustrations/astro-logo.png');
    }

    /**
     * Accessor for full favicon URL with fallback.
     */
    public function getFaviconUrlAttribute(): string
    {
        if (!empty($this->favicon)) {
            return asset('storage/' . $this->favicon);
        }

        return asset('assets/img/favicon/favicon.ico');
    }

    /**
     * Scope to quickly check if a specific social link is set.
     */
    public function scopeHasSocial($query, string $platform)
    {
        return $query->whereNotNull($platform)->where($platform, '!=', '');
    }
}
