<?php

namespace Modules\Accounting\Enums;

enum JournalEntryStatus: string
{
    case DRAFT = 'draft';
    case POSTED = 'posted';
    case CANCELLED = 'cancelled';

    //--------------------------------------------------
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => __('Draft'),
            self::POSTED => __('Posted'),
            self::CANCELLED => __('Cancelled'),
        };
    }

    //--------------------------------------------------
    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-label-warning',
            self::POSTED => 'bg-label-success',
            self::CANCELLED => 'bg-label-danger',
        };
    }

    //--------------------------------------------------
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
