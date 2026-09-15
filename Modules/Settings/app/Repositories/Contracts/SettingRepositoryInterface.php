<?php

namespace Modules\Settings\Repositories\Contracts;

use Modules\Settings\Models\Setting;

interface SettingRepositoryInterface
{
    /**
     * Get the current settings instance.
     */
    public function getSettings(): Setting;

    /**
     * Update settings data.
     *
     * @param array $data
     * @return Setting
     */
    public function updateSettings(array $data): Setting;
}
