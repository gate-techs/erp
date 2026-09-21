<?php

namespace Modules\HR\Enums;

enum ApplicationStage: string
{
    case NEW        = 'new';
    case SCREENING  = 'screening';
    case INTERVIEW  = 'interview';
    case OFFER      = 'offer';
    case HIRED      = 'hired';

    public function label(): string
    {
        return match ($this) {
            self::NEW       => __('Applied / New'),
            self::SCREENING => __('Screening / Shortlist'),
            self::INTERVIEW => __('Interviews'),
            self::OFFER     => __('Job Offer'),
            self::HIRED     => __('Hired'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::NEW       => 'bg-label-primary',
            self::SCREENING => 'bg-label-info',
            self::INTERVIEW => 'bg-label-warning',
            self::OFFER     => 'bg-label-dark',
            self::HIRED     => 'bg-label-success',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::NEW       => 'bx bx-envelope',
            self::SCREENING => 'bx bx-filter-alt',
            self::INTERVIEW => 'bx bx-calendar-event',
            self::OFFER     => 'bx bx-gift',
            self::HIRED     => 'bx bx-check-double',
        };
    }

    public function colorHex(): string
    {
        return match ($this) {
            self::NEW       => '#696cff',
            self::SCREENING => '#03c3ec',
            self::INTERVIEW => '#ffab00',
            self::OFFER     => '#8592a3',
            self::HIRED     => '#71dd37',
        };
    }

    public static function orderedStages(): array
    {
        return [
            self::NEW,
            self::SCREENING,
            self::INTERVIEW,
            self::OFFER,
            self::HIRED,
        ];
    }
}
