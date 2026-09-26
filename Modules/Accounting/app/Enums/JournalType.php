<?php

namespace Modules\Accounting\Enums;

enum JournalType: string
{
    case BANK = 'bank';
    case CASH = 'cash';
    case CREDIT_CARD = 'credit_card';
    case SALE = 'sale';
    case PURCHASE = 'purchase';
    case GENERAL = 'general';

    //--------------------------------------------------
    public function label(): string
    {
        return match ($this) {
            self::BANK => __('Bank'),
            self::CASH => __('Cash'),
            self::CREDIT_CARD => __('Credit Card'),
            self::SALE => __('Sales'),
            self::PURCHASE => __('Purchase'),
            self::GENERAL => __('Miscellaneous'),
        };
    }

    //--------------------------------------------------
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
