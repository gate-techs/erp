<?php

namespace Modules\Accounting\Enums;

enum JournalEntryType: string
{
    case MANUAL = 'manual';
    case SALES_INVOICE = 'sales_invoice';
    case VENDOR_BILL = 'vendor_bill';
    case PAYMENT = 'payment';
    case RECEIPT = 'receipt';
    case CLOSING = 'closing';
    case REVERSAL = 'reversal';
    case OPENING = 'opening';

    //--------------------------------------------------
    public function label(): string
    {
        return match ($this) {
            self::MANUAL => __('Manual Entry'),
            self::SALES_INVOICE => __('Sales Invoice'),
            self::VENDOR_BILL => __('Vendor Bill'),
            self::PAYMENT => __('Payment Voucher'),
            self::RECEIPT => __('Receipt Voucher'),
            self::CLOSING => __('Year-end Closing'),
            self::REVERSAL => __('Reversal Entry'),
            self::OPENING => __('Opening Entry'),
        };
    }

    //--------------------------------------------------
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
