<?php

namespace Modules\HR\Enums;

enum PostingType: string
{
    case INTERNAL = 'internal';
    case EXTERNAL = 'external';
    case BOTH     = 'both';

    public function label(): string
    {
        return match ($this) {
            self::INTERNAL => __('Internal Only'),
            self::EXTERNAL => __('External Only'),
            self::BOTH     => __('Internal & External'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::INTERNAL => 'bg-label-info',
            self::EXTERNAL => 'bg-label-primary',
            self::BOTH     => 'bg-label-success',
        };
    }
}
