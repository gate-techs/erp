<?php

namespace Modules\Settings\Enums;

enum NotificationAudience: string
{
    case ALL = 'all';
    case EMPLOYEES = 'employees';
    case TEACHERS = 'teachers';
    case GUARDIANS = 'guardians';

    public function label(): string
    {
        return match ($this) {
            self::ALL => __('Everyone'),
            self::EMPLOYEES => __('Employees'),
            self::TEACHERS => __('Teachers'),
            self::GUARDIANS => __('Guardians'),
        };
    }
}
