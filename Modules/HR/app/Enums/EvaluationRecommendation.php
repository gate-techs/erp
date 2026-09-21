<?php

namespace Modules\HR\Enums;

enum EvaluationRecommendation: string
{
    case STRONG_HIRE    = 'strong_hire';
    case HIRE           = 'hire';
    case NEUTRAL        = 'neutral';
    case DO_NOT_HIRE    = 'do_not_hire';
    case STRONG_NO_HIRE = 'strong_no_hire';

    public function label(): string
    {
        return match ($this) {
            self::STRONG_HIRE    => __('Strong Hire'),
            self::HIRE           => __('Hire'),
            self::NEUTRAL        => __('Neutral / On Hold'),
            self::DO_NOT_HIRE    => __('Do Not Hire'),
            self::STRONG_NO_HIRE => __('Strong No Hire'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::STRONG_HIRE    => 'bg-label-success',
            self::HIRE           => 'bg-label-primary',
            self::NEUTRAL        => 'bg-label-warning',
            self::DO_NOT_HIRE    => 'bg-label-danger',
            self::STRONG_NO_HIRE => 'bg-label-dark',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::STRONG_HIRE    => 'bx bx-like',
            self::HIRE           => 'bx bx-check',
            self::NEUTRAL        => 'bx bx-help-circle',
            self::DO_NOT_HIRE    => 'bx bx-dislike',
            self::STRONG_NO_HIRE => 'bx bx-block',
        };
    }
}
