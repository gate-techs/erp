<?php

namespace Modules\HR\Enums;

enum EmploymentType: string
{
    case FULL_TIME = 'full_time';
    case PART_TIME = 'part_time';
    case CONTRACT = 'contract';
    case TEMPORARY = 'temporary';
    case INTERN = 'intern';

    //======================
    /**
     * Get human-readable label for employment type.
     */
    public function label(): string
    {
        return match ($this) {
            self::FULL_TIME => __('Full Time'),
            self::PART_TIME => __('Part Time'),
            self::CONTRACT => __('Contract'),
            self::TEMPORARY => __('Temporary'),
            self::INTERN => __('Intern'),
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
