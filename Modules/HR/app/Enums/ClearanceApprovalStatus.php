<?php

namespace Modules\HR\Enums;

enum ClearanceApprovalStatus: string
{
    case PENDING  = 'pending';
    case CLEARED  = 'cleared';
    case HAS_DUES = 'has_dues';
    case REJECTED = 'rejected';

    /**
     * Get user-friendly translated label.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING  => __('Pending'),
            self::CLEARED  => __('Cleared / Approved'),
            self::HAS_DUES => __('Pending Dues / Assets'),
            self::REJECTED => __('Rejected'),
        };
    }

    /**
     * CSS badge classes for Sneat template.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING  => 'bg-label-warning text-warning',
            self::CLEARED  => 'bg-label-success text-success',
            self::HAS_DUES => 'bg-label-danger text-danger',
            self::REJECTED => 'bg-label-secondary text-secondary',
        };
    }

    /**
     * Boxicons representation.
     */
    public function icon(): string
    {
        return match ($this) {
            self::PENDING  => 'bx bx-time',
            self::CLEARED  => 'bx bx-check-circle',
            self::HAS_DUES => 'bx bx-error-circle',
            self::REJECTED => 'bx bx-x',
        };
    }
}

