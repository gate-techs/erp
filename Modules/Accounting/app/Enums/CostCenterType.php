<?php

namespace Modules\Accounting\Enums;

enum CostCenterType: string
{
    case GENERAL = 'general';
    case COST = 'cost';
    case PROFIT = 'profit';

    //===============================
    public function label(): string
    {
        return match ($this) {
            self::GENERAL => __('General'),
            self::COST => __('Cost Center'),
            self::PROFIT => __('Profit Center'),
        };
    }

    //===============================
    public function badgeClass(): string
    {
        return match ($this) {
            self::GENERAL => 'bg-label-info',
            self::COST => 'bg-label-warning',
            self::PROFIT => 'bg-label-success',
        };
    }

    //===============================
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
