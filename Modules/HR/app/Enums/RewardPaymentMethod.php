<?php

namespace Modules\HR\Enums;

enum RewardPaymentMethod: string
{
    case NEXT_PAYROLL      = 'next_payroll';
    case SEPARATE_PAYMENT  = 'separate_payment';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::NEXT_PAYROLL     => __('With Next Payroll'),
            self::SEPARATE_PAYMENT => __('Separate Direct Payment'),
        };
    }

    /**
     * Get badge class.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::NEXT_PAYROLL     => 'bg-label-info',
            self::SEPARATE_PAYMENT => 'bg-label-primary',
        };
    }

    /**
     * Get text color class.
     */
    public function textColor(): string
    {
        return match ($this) {
            self::NEXT_PAYROLL     => 'text-info',
            self::SEPARATE_PAYMENT => 'text-primary',
        };
    }

    /**
     * Get icon.
     */
    public function icon(): string
    {
        return match ($this) {
            self::NEXT_PAYROLL     => 'bx bx-receipt',
            self::SEPARATE_PAYMENT => 'bx bx-credit-card',
        };
    }
}
