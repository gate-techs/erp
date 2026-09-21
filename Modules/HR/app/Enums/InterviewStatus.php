<?php

namespace Modules\HR\Enums;

enum InterviewStatus: string
{
    case SCHEDULED   = 'scheduled';
    case COMPLETED   = 'completed';
    case RESCHEDULED = 'rescheduled';
    case CANCELLED   = 'cancelled';
    case NO_SHOW     = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED   => __('Scheduled'),
            self::COMPLETED   => __('Completed'),
            self::RESCHEDULED => __('Rescheduled'),
            self::CANCELLED   => __('Cancelled'),
            self::NO_SHOW     => __('No Show'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::SCHEDULED   => 'bg-label-primary',
            self::COMPLETED   => 'bg-label-success',
            self::RESCHEDULED => 'bg-label-warning',
            self::CANCELLED   => 'bg-label-secondary',
            self::NO_SHOW     => 'bg-label-danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::SCHEDULED   => 'bx bx-calendar-event',
            self::COMPLETED   => 'bx bx-check-circle',
            self::RESCHEDULED => 'bx bx-time-five',
            self::CANCELLED   => 'bx bx-x-circle',
            self::NO_SHOW     => 'bx bx-user-x',
        };
    }
}
