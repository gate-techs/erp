<?php

namespace Modules\HR\Enums;

enum PenaltyCalculationType: string
{
    case DAYS_DEDUCTION = 'days_deduction';
    case FIXED_AMOUNT   = 'fixed_amount';
    case PERCENTAGE     = 'percentage';
    case WARNING_ONLY   = 'warning_only';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::DAYS_DEDUCTION => __('Days Deduction'),
            self::FIXED_AMOUNT   => __('Fixed Amount'),
            self::PERCENTAGE     => __('Percentage of Basic Salary'),
            self::WARNING_ONLY   => __('Warning / Non-financial'),
        };
    }

    /**
     * Get unit symbol / hint.
     */
    public function unitLabel(): string
    {
        return match ($this) {
            self::DAYS_DEDUCTION => __('Days'),
            self::FIXED_AMOUNT   => __('KWD'),
            self::PERCENTAGE     => '%',
            self::WARNING_ONLY   => '-',
        };
    }

    /**
     * Get badge CSS class.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::DAYS_DEDUCTION => 'bg-label-danger',
            self::FIXED_AMOUNT   => 'bg-label-warning',
            self::PERCENTAGE     => 'bg-label-info',
            self::WARNING_ONLY   => 'bg-label-secondary',
        };
    }

    /**
     * Get text color CSS class.
     */
    public function textColor(): string
    {
        return match ($this) {
            self::DAYS_DEDUCTION => 'text-danger',
            self::FIXED_AMOUNT   => 'text-warning',
            self::PERCENTAGE     => 'text-info',
            self::WARNING_ONLY   => 'text-secondary',
        };
    }

    /**
     * Get icon class.
     */
    public function icon(): string
    {
        return match ($this) {
            self::DAYS_DEDUCTION => 'bx bx-calendar-x',
            self::FIXED_AMOUNT   => 'bx bx-money-withdraw',
            self::PERCENTAGE     => 'bx bx-pie-chart-alt',
            self::WARNING_ONLY   => 'bx bx-bell',
        };
    }
}
