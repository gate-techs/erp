<?php

namespace Modules\HR\Enums;

enum WorkScheduleType: string
{
    case FIXED = 'fixed';
    case FLEXIBLE = 'flexible';
    case ROTATIONAL = 'rotational';

    //======================
    /**
     * Get human-readable label for schedule type.
     */
    public function label(): string
    {
        return match ($this) {
            self::FIXED => __('Fixed Weekly'),
            self::FLEXIBLE => __('Flexible Hours'),
            self::ROTATIONAL => __('Rotational / Shifts'),
        };
    }

    //======================
    /**
     * Get badge color class for UI.
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::FIXED => 'bg-label-primary',
            self::FLEXIBLE => 'bg-label-info',
            self::ROTATIONAL => 'bg-label-warning',
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
