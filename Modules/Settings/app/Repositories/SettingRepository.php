<?php

namespace Modules\Settings\Repositories;

use Modules\Settings\Models\Setting;
use Modules\Settings\Repositories\Contracts\SettingRepositoryInterface;

class SettingRepository implements SettingRepositoryInterface
{
    /**
     * Get the current singleton settings instance.
     */
    public function getSettings(): Setting
    {
        return Setting::instance();
    }

    /**
     * Update settings and their translations.
     *
     * @param array $data
     * @return Setting
     */
    public function updateSettings(array $data): Setting
    {
        $setting = $this->getSettings();
        $setting->update($data);

        return $setting->fresh();
    }
}
