<?php

namespace Modules\HR\Enums;

enum InstallmentStatus: string
{
    case PENDING   = 'pending';
    case PAID      = 'paid';
    case POSTPONED = 'postponed';
    case CANCELLED = 'cancelled';

    /**
     * Get the human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING   => __('Pending Deduction'),
            self::PAID      => __('Paid'),
            self::POSTPONED => __('Postponed'),
            self::CANCELLED => __('Cancelled'),
        };
    }

    /**
     * Get the badge CSS class.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING   => 'bg-label-warning',
            self::PAID      => 'bg-label-success',
            self::POSTPONED => 'bg-label-info',
            self::CANCELLED => 'bg-label-secondary',
        };
    }

    /**
     * Get the icon class.
     */
    public function icon(): string
    {
        return match ($this) {
            self::PENDING   => 'bx bx-time-five',
            self::PAID      => 'bx bx-check-circle',
            self::POSTPONED => 'bx bx-calendar-event',
            self::CANCELLED => 'bx bx-x',
        };
    }
}
