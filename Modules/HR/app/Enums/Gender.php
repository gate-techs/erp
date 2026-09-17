<?php

namespace Modules\HR\Enums;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';

    //======================
    /**
     * Get human-readable label for gender.
     */
    public function label(): string
    {
        return match ($this) {
            self::MALE => __('Male'),
            self::FEMALE => __('Female'),
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
