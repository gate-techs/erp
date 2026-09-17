<?php

namespace Modules\HR\Enums;

enum EmployeeStatus: string
{
    case ACTIVE = 'active';
    case ON_LEAVE = 'on_leave';
    case PROBATION = 'probation';
    case SUSPENDED = 'suspended';
    case RESIGNED = 'resigned';
    case TERMINATED = 'terminated';

    //======================
    /**
     * Get human-readable label for status.
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('Active'),
            self::ON_LEAVE => __('On Leave'),
            self::PROBATION => __('Probation'),
            self::SUSPENDED => __('Suspended'),
            self::RESIGNED => __('Resigned'),
            self::TERMINATED => __('Terminated'),
        };
    }

    //======================
    /**
     * Get badge color class for UI.
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::ACTIVE => 'bg-label-success',
            self::ON_LEAVE => 'bg-label-info',
            self::PROBATION => 'bg-label-warning',
            self::SUSPENDED => 'bg-label-danger',
            self::RESIGNED => 'bg-label-secondary',
            self::TERMINATED => 'bg-label-dark',
        };
    }

    //======================
    /**
     * Get all values as array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
