<?php

namespace Modules\Accounting\Enums;

enum BankReconciliationStatus: string
{
    case DRAFT = 'draft';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    //--------------------------------------------------
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => __('Draft'),
            self::IN_PROGRESS => __('In Progress'),
            self::COMPLETED => __('Reconciled & Closed'),
            self::CANCELLED => __('Cancelled'),
        };
    }

    //--------------------------------------------------
    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT => 'bg-label-secondary',
            self::IN_PROGRESS => 'bg-label-warning',
            self::COMPLETED => 'bg-label-success',
            self::CANCELLED => 'bg-label-danger',
        };
    }

    //--------------------------------------------------
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
