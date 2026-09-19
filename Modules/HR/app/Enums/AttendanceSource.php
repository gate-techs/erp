<?php

namespace Modules\HR\Enums;

enum AttendanceSource: string
{
    case MANUAL = 'manual';
    case BIOMETRIC_DEVICE = 'biometric_device';
    case MOBILE_APP = 'mobile_app';
    case EXCEL_IMPORT = 'excel_import';

    //======================
    /**
     * Get human-readable label for source.
     */
    public function label(): string
    {
        return match ($this) {
            self::MANUAL => __('Manual (Dashboard)'),
            self::BIOMETRIC_DEVICE => __('Biometric Device'),
            self::MOBILE_APP => __('Mobile App'),
            self::EXCEL_IMPORT => __('Excel Import'),
        };
    }

    //======================
    /**
     * Get icon representation.
     */
    public function iconClass(): string
    {
        return match ($this) {
            self::MANUAL => 'bx bx-desktop text-primary',
            self::BIOMETRIC_DEVICE => 'bx bx-fingerprint text-info',
            self::MOBILE_APP => 'bx bx-mobile-alt text-success',
            self::EXCEL_IMPORT => 'bx bx-file text-warning',
        };
    }

    //======================
    /**
     * Get all values as array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
