<?php

namespace Modules\HR\Enums;

enum RequisitionType: string
{
    case EXPANSION   = 'expansion';
    case REPLACEMENT = 'replacement';
    case TEMPORARY   = 'temporary';
    case SEASONAL    = 'seasonal';

    public function label(): string
    {
        return match ($this) {
            self::EXPANSION   => __('New Position / Expansion'),
            self::REPLACEMENT => __('Replacement for Resigned/Terminated'),
            self::TEMPORARY   => __('Temporary / Project-based'),
            self::SEASONAL    => __('Seasonal Work'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::EXPANSION   => 'bg-label-primary',
            self::REPLACEMENT => 'bg-label-warning',
            self::TEMPORARY   => 'bg-label-info',
            self::SEASONAL    => 'bg-label-secondary',
        };
    }
}
