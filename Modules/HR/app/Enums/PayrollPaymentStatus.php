<?php

namespace Modules\HR\Enums;

enum PayrollPaymentStatus: string
{
    case PENDING = 'pending';
    case PAID    = 'paid';

    /**
     * Human-readable localized label.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('Pending Payment'),
            self::PAID    => __('Paid'),
        };
    }

    /**
     * Clean typography text color class.
     */
    public function textColor(): string
    {
        return match ($this) {
            self::PENDING => 'text-warning',
            self::PAID    => 'text-success',
        };
    }

    /**
     * Badge CSS class.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING => 'bg-label-warning',
            self::PAID    => 'bg-label-success',
        };
    }

    /**
     * Associated Boxicons icon.
     */
    public function icon(): string
    {
        return match ($this) {
            self::PENDING => 'bx-time-five',
            self::PAID    => 'bx-check-circle',
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
