<?php

namespace Modules\Accounting\Enums;

enum AccountType: string
{
    case ASSET = 'asset';
    case LIABILITY = 'liability';
    case EQUITY = 'equity';
    case INCOME = 'income';
    case EXPENSE = 'expense';
    case OFF_BALANCE = 'off_balance';

    //--------------------------------------------------
    public function normalBalance(): string
    {
        return match ($this) {
            self::ASSET, self::EXPENSE => 'debit',
            self::LIABILITY, self::EQUITY, self::INCOME, self::OFF_BALANCE => 'credit',
        };
    }

    //--------------------------------------------------
    public function label(): string
    {
        return match ($this) {
            self::ASSET => __('Asset'),
            self::LIABILITY => __('Liability'),
            self::EQUITY => __('Equity'),
            self::INCOME => __('Income'),
            self::EXPENSE => __('Expense'),
            self::OFF_BALANCE => __('Off-balance sheet'),
        };
    }

    //--------------------------------------------------
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
