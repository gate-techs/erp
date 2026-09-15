<?php

namespace Modules\Settings\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Settings\Models\Setting;
use Modules\Settings\Repositories\Contracts\SettingRepositoryInterface;

class SettingService
{
    public function __construct(
        protected SettingRepositoryInterface $settingRepository
    ) {}

    /**
     * Get system settings.
     */
    public function getSettings(): Setting
    {
        return $this->settingRepository->getSettings();
    }

    /**
     * Update system settings with file uploads handling.
     *
     * @param array $data
     * @param UploadedFile|null $logo
     * @param UploadedFile|null $favicon
     * @return Setting
     */
    public function updateSettings(array $data, ?UploadedFile $logo = null, ?UploadedFile $favicon = null): Setting
    {
        $currentSetting = $this->getSettings();

        // Handle Logo Upload via Centralized Upload Helper
        if ($logo) {
            $data['logo'] = upload_image($logo, 'settings', $currentSetting->logo);
        }

        // Handle Favicon Upload via Centralized Upload Helper
        if ($favicon) {
            $data['favicon'] = upload_image($favicon, 'settings', $currentSetting->favicon);
        }

        return $this->settingRepository->updateSettings($data);
    }

    /**
     * Format settings as structured array for API / Mobile consumers.
     *
     * @param string|null $locale
     * @return array
     */
    public function getFormattedSettingsForApi(?string $locale = null): array
    {
        $setting = $this->getSettings();
        $activeLocale = $locale ?? app()->getLocale();

        return [
            'general' => [
                'site_name' => $setting->translate($activeLocale)?->site_name ?? $setting->site_name,
                'slogan' => $setting->translate($activeLocale)?->slogan ?? $setting->slogan,
                'address' => $setting->translate($activeLocale)?->address ?? $setting->address,
                'footer_text' => $setting->translate($activeLocale)?->footer_text ?? $setting->footer_text,
                'logo_url' => $setting->logo_url,
                'favicon_url' => $setting->favicon_url,
                'primary_color' => $setting->primary_color ?? '#0b284e',
            ],
            'contact' => [
                'email' => $setting->email,
                'phone' => $setting->phone,
                'whatsapp' => $setting->whatsapp,
                'emergency_phone' => $setting->emergency_phone,
            ],
            'financial' => [
                'currency' => $setting->currency,
                'currency_symbol' => $setting->currency_symbol,
                'tax_number' => $setting->tax_number,
            ],
            'localization' => [
                'timezone' => $setting->timezone,
                'default_locale' => $setting->default_locale,
            ],
            'social' => [
                'facebook' => $setting->facebook,
                'instagram' => $setting->instagram,
                'twitter' => $setting->twitter,
                'tiktok' => $setting->tiktok,
                'youtube' => $setting->youtube,
            ],
            'translations' => [
                'ar' => [
                    'site_name' => $setting->translate('ar')?->site_name,
                    'slogan' => $setting->translate('ar')?->slogan,
                    'address' => $setting->translate('ar')?->address,
                    'footer_text' => $setting->translate('ar')?->footer_text,
                ],
                'en' => [
                    'site_name' => $setting->translate('en')?->site_name,
                    'slogan' => $setting->translate('en')?->slogan,
                    'address' => $setting->translate('en')?->address,
                    'footer_text' => $setting->translate('en')?->footer_text,
                ],
            ],
        ];
    }
}
