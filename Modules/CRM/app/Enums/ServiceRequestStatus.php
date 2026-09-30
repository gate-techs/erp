<?php

namespace Modules\CRM\Enums;

enum ServiceRequestStatus: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'inprogress';
    case COMPLETE = 'complete';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => __('Pending'),
            self::IN_PROGRESS => __('In Progress'),
            self::COMPLETE => __('Complete'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
