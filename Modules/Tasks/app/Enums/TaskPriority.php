<?php

namespace Modules\Tasks\Enums;

enum TaskPriority: string
{
    case LOW    = 'low';
    case MEDIUM = 'medium';
    case HIGH   = 'high';
    case URGENT = 'urgent';

    //-----------------------------------------
    /**
     * Get the display label for the priority.
     */
    public function label(): string
    {
        return match ($this) {
            self::LOW    => __('Low Priority'),
            self::MEDIUM => __('Medium Priority'),
            self::HIGH   => __('High Priority'),
            self::URGENT => __('Urgent Priority'),
        };
    }

    //-----------------------------------------
    /**
     * Get badge color class for Sneat / Bootstrap 5.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::LOW    => 'bg-label-info',
            self::MEDIUM => 'bg-label-primary',
            self::HIGH   => 'bg-label-warning',
            self::URGENT => 'bg-label-danger',
        };
    }

    //-----------------------------------------
    /**
     * Get all enum values.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
