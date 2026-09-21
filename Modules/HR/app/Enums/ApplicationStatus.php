<?php

namespace Modules\HR\Enums;

enum ApplicationStatus: string
{
    case ACTIVE    = 'active';
    case HIRED     = 'hired';
    case REJECTED  = 'rejected';
    case WITHDRAWN = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE    => __('Active'),
            self::HIRED     => __('Hired'),
            self::REJECTED  => __('Rejected / Disqualified'),
            self::WITHDRAWN => __('Candidate Withdrawn'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE    => 'bg-label-success',
            self::HIRED     => 'bg-label-primary',
            self::REJECTED  => 'bg-label-danger',
            self::WITHDRAWN => 'bg-label-secondary',
        };
    }
}
