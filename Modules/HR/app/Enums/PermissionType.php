<?php

namespace Modules\HR\Enums;

enum PermissionType: string
{
    case LATE_ARRIVAL    = 'late_arrival';
    case EARLY_DEPARTURE = 'early_departure';
    case DURING_SHIFT    = 'during_shift';
    case EMERGENCY       = 'emergency';

    //======================
    /**
     * Get user-friendly translated label.
     */
    public function label(): string
    {
        return match ($this) {
            self::LATE_ARRIVAL    => __('Late Arrival'),
            self::EARLY_DEPARTURE => __('Early Departure'),
            self::DURING_SHIFT    => __('During Shift'),
            self::EMERGENCY       => __('Emergency Permission'),
        };
    }

    //======================
    /**
     * CSS badge classes for Sneat template.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::LATE_ARRIVAL    => 'bg-label-warning text-warning',
            self::EARLY_DEPARTURE => 'bg-label-info text-info',
            self::DURING_SHIFT    => 'bg-label-primary text-primary',
            self::EMERGENCY       => 'bg-label-danger text-danger',
        };
    }

    //======================
    /**
     * Boxicons representation.
     */
    public function icon(): string
    {
        return match ($this) {
            self::LATE_ARRIVAL    => 'bx bx-time',
            self::EARLY_DEPARTURE => 'bx bx-log-out-circle',
            self::DURING_SHIFT    => 'bx bx-transfer',
            self::EMERGENCY       => 'bx bx-alarm-exclamation',
        };
    }

    //======================
    /**
     * Return all values.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
