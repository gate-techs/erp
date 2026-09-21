<?php

namespace Modules\HR\Enums;

enum OnboardingTaskCategory: string
{
    case DOCUMENTATION = 'documentation';
    case IT_ACCESS     = 'it_access';
    case ORIENTATION   = 'orientation';
    case FACILITIES    = 'facilities';

    public function label(): string
    {
        return match ($this) {
            self::DOCUMENTATION => __('Documentation & Contracts'),
            self::IT_ACCESS     => __('IT Systems & Equipment'),
            self::ORIENTATION   => __('Orientation & Culture'),
            self::FACILITIES    => __('Workplace & Facilities'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DOCUMENTATION => 'bg-label-info',
            self::IT_ACCESS     => 'bg-label-primary',
            self::ORIENTATION   => 'bg-label-success',
            self::FACILITIES    => 'bg-label-warning',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::DOCUMENTATION => 'bx bx-file',
            self::IT_ACCESS     => 'bx bx-laptop',
            self::ORIENTATION   => 'bx bx-group',
            self::FACILITIES    => 'bx bx-building-house',
        };
    }
}
