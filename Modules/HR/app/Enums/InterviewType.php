<?php

namespace Modules\HR\Enums;

enum InterviewType: string
{
    case PHONE_SCREEN = 'phone_screen';
    case TECHNICAL    = 'technical';
    case HR           = 'hr';
    case MANAGERIAL   = 'managerial';
    case ASSESSMENT   = 'assessment';
    case FINAL        = 'final';

    public function label(): string
    {
        return match ($this) {
            self::PHONE_SCREEN => __('Phone Screen'),
            self::TECHNICAL    => __('Technical Interview'),
            self::HR           => __('HR Interview'),
            self::MANAGERIAL   => __('Managerial Interview'),
            self::ASSESSMENT   => __('Practical Assessment'),
            self::FINAL        => __('Final Interview'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::PHONE_SCREEN => 'bg-label-secondary',
            self::TECHNICAL    => 'bg-label-primary',
            self::HR           => 'bg-label-info',
            self::MANAGERIAL   => 'bg-label-warning',
            self::ASSESSMENT   => 'bg-label-dark',
            self::FINAL        => 'bg-label-success',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::PHONE_SCREEN => 'bx bx-phone-call',
            self::TECHNICAL    => 'bx bx-code-alt',
            self::HR           => 'bx bx-user-voice',
            self::MANAGERIAL   => 'bx bx-briefcase',
            self::ASSESSMENT   => 'bx bx-task',
            self::FINAL        => 'bx bx-award',
        };
    }
}
