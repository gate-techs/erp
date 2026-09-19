<?php

namespace Modules\HR\Enums;

enum PunchType: string
{
    case CHECK_IN = 'check_in';
    case CHECK_OUT = 'check_out';
    case AUTO = 'auto';

    //======================
    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::CHECK_IN => __('Check In'),
            self::CHECK_OUT => __('Check Out'),
            self::AUTO => __('Auto Detect'),
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
