<?php

namespace Modules\HR\Enums;

enum PlanItemPriority: string
{
    case LOW      = 'low';
    case MEDIUM   = 'medium';
    case HIGH     = 'high';
    case CRITICAL = 'critical';

    public function label(): string
    {
        return match ($this) {
            self::LOW      => __('Low'),
            self::MEDIUM   => __('Medium'),
            self::HIGH     => __('High'),
            self::CRITICAL => __('Critical / Urgent'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::LOW      => 'bg-label-secondary',
            self::MEDIUM   => 'bg-label-info',
            self::HIGH     => 'bg-label-warning',
            self::CRITICAL => 'bg-label-danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::LOW      => 'bx bx-chevron-down',
            self::MEDIUM   => 'bx bx-minus',
            self::HIGH     => 'bx bx-chevron-up',
            self::CRITICAL => 'bx bx-error',
        };
    }
}
