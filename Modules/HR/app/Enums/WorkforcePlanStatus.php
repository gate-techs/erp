<?php

namespace Modules\HR\Enums;

enum WorkforcePlanStatus: string
{
    case DRAFT     = 'draft';
    case SUBMITTED = 'submitted';
    case APPROVED  = 'approved';
    case ACTIVE    = 'active';
    case CLOSED    = 'closed';
    case CANCELLED = 'cancelled';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT     => __('Draft'),
            self::SUBMITTED => __('Submitted for Approval'),
            self::APPROVED  => __('Approved'),
            self::ACTIVE    => __('Active / In Progress'),
            self::CLOSED    => __('Closed / Fulfilled'),
            self::CANCELLED => __('Cancelled'),
        };
    }

    /**
     * Get badge CSS class.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT     => 'bg-label-secondary',
            self::SUBMITTED => 'bg-label-warning',
            self::APPROVED  => 'bg-label-info',
            self::ACTIVE    => 'bg-label-success',
            self::CLOSED    => 'bg-label-dark',
            self::CANCELLED => 'bg-label-danger',
        };
    }

    /**
     * Get Boxicons icon class.
     */
    public function icon(): string
    {
        return match ($this) {
            self::DRAFT     => 'bx bx-edit',
            self::SUBMITTED => 'bx bx-time-five',
            self::APPROVED  => 'bx bx-check',
            self::ACTIVE    => 'bx bx-play-circle',
            self::CLOSED    => 'bx bx-check-double',
            self::CANCELLED => 'bx bx-x-circle',
        };
    }
}
