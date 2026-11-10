<?php

namespace Modules\HR\Enums;

enum ExitClearanceStatus: string
{
    case PENDING     = 'pending';
    case IN_PROGRESS = 'in_progress';
    case CLEARED     = 'cleared';
    case REJECTED    = 'rejected';
    case CANCELLED   = 'cancelled';

    /**
     * Get user-friendly translated label.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING     => __('Pending Review'),
            self::IN_PROGRESS => __('In Progress'),
            self::CLEARED     => __('Fully Cleared'),
            self::REJECTED    => __('Rejected'),
            self::CANCELLED   => __('Cancelled'),
        };
    }

    /**
     * CSS badge classes for Sneat template.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING     => 'bg-label-warning text-warning',
            self::IN_PROGRESS => 'bg-label-info text-info',
            self::CLEARED     => 'bg-label-success text-success',
            self::REJECTED    => 'bg-label-danger text-danger',
            self::CANCELLED   => 'bg-label-secondary text-secondary',
        };
    }

    /**
     * Boxicons representation.
     */
    public function icon(): string
    {
        return match ($this) {
            self::PENDING     => 'bx bx-time-five',
            self::IN_PROGRESS => 'bx bx-sync',
            self::CLEARED     => 'bx bx-check-double',
            self::REJECTED    => 'bx bx-x-circle',
            self::CANCELLED   => 'bx bx-block',
        };
    }
}

