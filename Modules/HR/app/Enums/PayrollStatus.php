<?php

namespace Modules\HR\Enums;

enum PayrollStatus: string
{
    case DRAFT     = 'draft';
    case APPROVED  = 'approved';
    case PAID      = 'paid';
    case CANCELLED = 'cancelled';

    /**
     * Human-readable localized label.
     */
    public function label(): string
    {
        return match ($this) {
            self::DRAFT     => __('Draft'),
            self::APPROVED  => __('Approved'),
            self::PAID      => __('Paid / Disbursed'),
            self::CANCELLED => __('Cancelled'),
        };
    }

    /**
     * Clean typography text color class (without bulky pill badge).
     */
    public function textColor(): string
    {
        return match ($this) {
            self::DRAFT     => 'text-secondary',
            self::APPROVED  => 'text-info',
            self::PAID      => 'text-success',
            self::CANCELLED => 'text-danger',
        };
    }

    /**
     * Associated Boxicons icon.
     */
    public function icon(): string
    {
        return match ($this) {
            self::DRAFT     => 'bx-edit-alt',
            self::APPROVED  => 'bx-check-circle',
            self::PAID      => 'bx-check-double',
            self::CANCELLED => 'bx-x-circle',
        };
    }

    /**
     * Badge background class for modals or highlights.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT     => 'bg-label-secondary',
            self::APPROVED  => 'bg-label-info',
            self::PAID      => 'bg-label-success',
            self::CANCELLED => 'bg-label-danger',
        };
    }

    /**
     * List all enum values.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
