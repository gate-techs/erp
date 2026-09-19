<?php

namespace Modules\HR\Enums;

enum PenaltyStatus: string
{
    case PENDING   = 'pending';
    case APPROVED  = 'approved';
    case DEDUCTED  = 'deducted';
    case CANCELLED = 'cancelled';

    /**
     * Get the human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING   => __('Pending Approval'),
            self::APPROVED  => __('Approved'),
            self::DEDUCTED  => __('Deducted'),
            self::CANCELLED => __('Cancelled / Waived'),
        };
    }

    /**
     * Get the badge CSS class.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING   => 'bg-label-warning',
            self::APPROVED  => 'bg-label-info',
            self::DEDUCTED  => 'bg-label-danger',
            self::CANCELLED => 'bg-label-secondary',
        };
    }

    /**
     * Get the text color CSS class.
     */
    public function textColor(): string
    {
        return match ($this) {
            self::PENDING   => 'text-warning',
            self::APPROVED  => 'text-info',
            self::DEDUCTED  => 'text-danger',
            self::CANCELLED => 'text-secondary',
        };
    }

    /**
     * Get the icon class.
     */
    public function icon(): string
    {
        return match ($this) {
            self::PENDING   => 'bx bx-time-five',
            self::APPROVED  => 'bx bx-check',
            self::DEDUCTED  => 'bx bx-minus-circle',
            self::CANCELLED => 'bx bx-block',
        };
    }
}
