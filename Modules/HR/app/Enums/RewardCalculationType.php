<?php

namespace Modules\HR\Enums;

enum RewardCalculationType: string
{
    case FIXED_AMOUNT     = 'fixed_amount';
    case PERCENTAGE       = 'percentage';
    case DAYS_MULTIPLIER  = 'days_multiplier';

    /**
     * Get the human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::FIXED_AMOUNT     => __('Fixed Amount'),
            self::PERCENTAGE       => __('Percentage of Basic Salary'),
            self::DAYS_MULTIPLIER  => __('Days Wage Multiplier'),
        };
    }

    /**
     * Get the unit symbol / hint.
     */
    public function unitLabel(): string
    {
        return match ($this) {
            self::FIXED_AMOUNT     => __('KWD'),
            self::PERCENTAGE       => '%',
            self::DAYS_MULTIPLIER  => __('Days'),
        };
    }

    /**
     * Get badge class.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::FIXED_AMOUNT     => 'bg-label-primary',
            self::PERCENTAGE       => 'bg-label-success',
            self::DAYS_MULTIPLIER  => 'bg-label-info',
        };
    }

    /**
     * Get text color class.
     */
    public function textColor(): string
    {
        return match ($this) {
            self::FIXED_AMOUNT     => 'text-primary',
            self::PERCENTAGE       => 'text-success',
            self::DAYS_MULTIPLIER  => 'text-info',
        };
    }

    /**
     * Get icon.
     */
    public function icon(): string
    {
        return match ($this) {
            self::FIXED_AMOUNT     => 'bx bx-money',
            self::PERCENTAGE       => 'bx bx-pie-chart-alt',
            self::DAYS_MULTIPLIER  => 'bx bx-calendar-check',
        };
    }
}
