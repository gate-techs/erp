<?php

namespace Modules\HR\Enums;

enum InterviewMode: string
{
    case IN_PERSON = 'in_person';
    case ONLINE    = 'online';
    case PHONE     = 'phone';

    public function label(): string
    {
        return match ($this) {
            self::IN_PERSON => __('In-Person (Office)'),
            self::ONLINE    => __('Online (Video Call)'),
            self::PHONE     => __('Phone Call'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::IN_PERSON => 'bx bx-building',
            self::ONLINE    => 'bx bx-video',
            self::PHONE     => 'bx bx-phone',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::IN_PERSON => 'bg-label-primary',
            self::ONLINE    => 'bg-label-success',
            self::PHONE     => 'bg-label-info',
        };
    }
}
