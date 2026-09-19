<?php

namespace Modules\HR\Enums;

enum LeaveRequestStatus: string
{
    case PENDING          = 'pending';
    case MANAGER_APPROVED = 'manager_approved';
    case APPROVED         = 'approved';
    case REJECTED         = 'rejected';
    case CANCELLED        = 'cancelled';

    //======================
    /**
     * Get human-readable localized label.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING          => __('Pending Approval'),
            self::MANAGER_APPROVED => __('Manager Approved'),
            self::APPROVED         => __('Approved'),
            self::REJECTED         => __('Rejected'),
            self::CANCELLED        => __('Cancelled'),
        };
    }

    //======================
    /**
     * Get CSS badge class for UI representation.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING          => 'bg-label-warning text-warning',
            self::MANAGER_APPROVED => 'bg-label-info text-info',
            self::APPROVED         => 'bg-label-success text-success',
            self::REJECTED         => 'bg-label-danger text-danger',
            self::CANCELLED        => 'bg-label-secondary text-secondary',
        };
    }

    //======================
    /**
     * Get icon representation for status.
     */
    public function icon(): string
    {
        return match ($this) {
            self::PENDING          => 'bx bx-time-five',
            self::MANAGER_APPROVED => 'bx bx-user-check',
            self::APPROVED         => 'bx bx-check-double',
            self::REJECTED         => 'bx bx-x-circle',
            self::CANCELLED        => 'bx bx-block',
        };
    }

    //======================
    /**
     * Return all enum cases as array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
