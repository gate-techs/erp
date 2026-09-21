<?php

namespace Modules\HR\Enums;

enum JobOpeningStatus: string
{
    case DRAFT      = 'draft';
    case PUBLISHED  = 'published';
    case ON_HOLD    = 'on_hold';
    case CLOSED     = 'closed';
    case CANCELLED  = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT     => __('Draft'),
            self::PUBLISHED => __('Published / Active'),
            self::ON_HOLD   => __('On Hold'),
            self::CLOSED    => __('Closed'),
            self::CANCELLED => __('Cancelled'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT     => 'bg-label-secondary',
            self::PUBLISHED => 'bg-label-success',
            self::ON_HOLD   => 'bg-label-warning',
            self::CLOSED    => 'bg-label-info',
            self::CANCELLED => 'bg-label-danger',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::DRAFT     => 'bx bx-edit',
            self::PUBLISHED => 'bx bx-broadcast',
            self::ON_HOLD   => 'bx bx-pause-circle',
            self::CLOSED    => 'bx bx-check-circle',
            self::CANCELLED => 'bx bx-x-circle',
        };
    }
}
