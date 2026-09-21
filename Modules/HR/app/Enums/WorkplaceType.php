<?php

namespace Modules\HR\Enums;

enum WorkplaceType: string
{
    case ON_SITE = 'on_site';
    case REMOTE  = 'remote';
    case HYBRID  = 'hybrid';

    public function label(): string
    {
        return match ($this) {
            self::ON_SITE => __('On-Site / Office'),
            self::REMOTE  => __('Remote / Work from Home'),
            self::HYBRID  => __('Hybrid'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ON_SITE => 'bg-label-primary',
            self::REMOTE  => 'bg-label-success',
            self::HYBRID  => 'bg-label-warning',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ON_SITE => 'bx bx-buildings',
            self::REMOTE  => 'bx bx-laptop',
            self::HYBRID  => 'bx bx-shuffle',
        };
    }
}
