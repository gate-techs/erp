<?php

namespace Modules\HR\Enums;

enum PenaltyDeductionMethod: string
{
    case NEXT_PAYROLL      = 'next_payroll';
    case DIRECT_DEDUCTION  = 'direct_deduction';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::NEXT_PAYROLL     => __('With Next Payroll'),
            self::DIRECT_DEDUCTION => __('Direct Salary Deduction'),
        };
    }

    /**
     * Get badge CSS class.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::NEXT_PAYROLL     => 'bg-label-info',
            self::DIRECT_DEDUCTION => 'bg-label-primary',
        };
    }

    /**
     * Get text color class.
     */
    public function textColor(): string
    {
        return match ($this) {
            self::NEXT_PAYROLL     => 'text-info',
            self::DIRECT_DEDUCTION => 'text-primary',
        };
    }

    /**
     * Get icon.
     */
    public function icon(): string
    {
        return match ($this) {
            self::NEXT_PAYROLL     => 'bx bx-receipt',
            self::DIRECT_DEDUCTION => 'bx bx-credit-card',
        };
    }
}
