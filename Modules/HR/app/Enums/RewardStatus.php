<?php

namespace Modules\HR\Enums;

enum RewardStatus: string
{
    case PENDING   = 'pending';
    case APPROVED  = 'approved';
    case PAID      = 'paid';
    case REJECTED  = 'rejected';
    case CANCELLED = 'cancelled';

    /**
     * Get the human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING   => __('Pending Approval'),
            self::APPROVED  => __('Approved'),
            self::PAID      => __('Paid'),
            self::REJECTED  => __('Rejected'),
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
            self::APPROVED  => 'bg-label-info',
            self::PAID      => 'bg-label-success',
            self::REJECTED  => 'bg-label-danger',
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
            self::PAID      => 'text-success',
            self::REJECTED  => 'text-danger',
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
            self::PAID      => 'bx bx-check-double',
            self::REJECTED  => 'bx bx-x-circle',
            self::CANCELLED => 'bx bx-block',
        };
    }
}
