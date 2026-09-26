<?php

namespace Modules\Accounting\Enums;

enum CashAccountType: string
{
    case CASH = 'cash';
    case BANK = 'bank';

    //--------------------------------------------------
    public function label(): string
    {
        return match ($this) {
            self::CASH => __('Cash Register'),
            self::BANK => __('Bank Account'),
        };
    }

    //--------------------------------------------------
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
