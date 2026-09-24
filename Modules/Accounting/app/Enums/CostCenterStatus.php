<?php

namespace Modules\Accounting\Enums;

enum CostCenterStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    //===============================
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => __('Active'),
            self::INACTIVE => __('Inactive'),
        };
    }

    //===============================
    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE => 'bg-label-success',
            self::INACTIVE => 'bg-label-danger',
        };
    }

    //===============================
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
