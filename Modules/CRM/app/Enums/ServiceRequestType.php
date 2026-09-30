<?php

namespace Modules\CRM\Enums;

enum ServiceRequestType: string
{
    case REGISTRATION_REQUEST = 'registration_request';
    case REGISTRATION_COMPLAINT = 'registration_complaint';

    public function label(): string
    {
        return match ($this) {
            self::REGISTRATION_REQUEST => __('Registration Request'),
            self::REGISTRATION_COMPLAINT => __('Registration Complaint'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
