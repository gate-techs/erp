<?php

namespace Modules\HR\Enums;

enum HrSettingStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    //======================
    /**
     * Get human-readable label for status.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE   => __('Active'),
            self::INACTIVE => __('Inactive'),
        };
    }

    //======================
    /**
     * Get badge color class for UI.
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::ACTIVE   => 'bg-label-success',
            self::INACTIVE => 'bg-label-danger',
        };
    }

    //======================
    /**
     * Get all values as array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
