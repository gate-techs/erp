<?php

namespace Modules\HR\Enums;

enum MaritalStatus: string
{
    case SINGLE = 'single';
    case MARRIED = 'married';
    case DIVORCED = 'divorced';
    case WIDOWED = 'widowed';

    //======================
    /**
     * Get human-readable label for marital status.
     */
    public function label(): string
    {
        return match ($this) {
            self::SINGLE => __('Single'),
            self::MARRIED => __('Married'),
            self::DIVORCED => __('Divorced'),
            self::WIDOWED => __('Widowed'),
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
