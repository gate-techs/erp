<?php

namespace Modules\HR\Enums;

enum OnboardingStatus: string
{
    case PENDING     = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED   = 'completed';
    case CANCELLED   = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING     => __('Pending'),
            self::IN_PROGRESS => __('In Progress'),
            self::COMPLETED   => __('Completed'),
            self::CANCELLED   => __('Cancelled'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING     => 'bg-label-warning',
            self::IN_PROGRESS => 'bg-label-primary',
            self::COMPLETED   => 'bg-label-success',
            self::CANCELLED   => 'bg-label-secondary',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PENDING     => 'bx bx-time',
            self::IN_PROGRESS => 'bx bx-loader-circle',
            self::COMPLETED   => 'bx bx-check-double',
            self::CANCELLED   => 'bx bx-x',
        };
    }
}
