<?php

namespace Modules\Accounting\Enums;

enum BankReconciliationItemType: string
{
    case MATCHED_ENTRY = 'matched_entry';
    case BANK_FEE = 'bank_fee';
    case INTEREST = 'interest';
    case ADJUSTMENT = 'adjustment';
    case UNRECORDED = 'unrecorded';

    //--------------------------------------------------
    public function label(): string
    {
        return match ($this) {
            self::MATCHED_ENTRY => __('Matched Entry'),
            self::BANK_FEE => __('Bank Fee / Charges'),
            self::INTEREST => __('Bank Interest / Profit'),
            self::ADJUSTMENT => __('Bank Adjustment'),
            self::UNRECORDED => __('Unrecorded Bank Item'),
        };
    }

    //--------------------------------------------------
    public function badgeClass(): string
    {
        return match ($this) {
            self::MATCHED_ENTRY => 'bg-label-primary',
            self::BANK_FEE => 'bg-label-danger',
            self::INTEREST => 'bg-label-success',
            self::ADJUSTMENT => 'bg-label-warning',
            self::UNRECORDED => 'bg-label-info',
        };
    }

    //--------------------------------------------------
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
