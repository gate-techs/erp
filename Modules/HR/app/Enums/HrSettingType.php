<?php

namespace Modules\HR\Enums;

enum HrSettingType: string
{
    case ALLOWANCE_TYPES = 'allowance-types';
    case DOCUMENT_TYPES = 'document-types';
    case VISA_TYPES = 'visa-types';

    //======================
    /**
     * Get human-readable label for the setting type.
     */
    public function label(): string
    {
        return match ($this) {
            self::ALLOWANCE_TYPES => __('Allowance Types'),
            self::DOCUMENT_TYPES  => __('Document Types'),
            self::VISA_TYPES      => __('Visa Types'),
        };
    }

    //======================
    /**
     * Get badge color class for UI.
     */
    public function badgeColor(): string
    {
        return match ($this) {
            self::ALLOWANCE_TYPES => 'bg-label-primary',
            self::DOCUMENT_TYPES  => 'bg-label-info',
            self::VISA_TYPES      => 'bg-label-warning',
        };
    }

    //======================
    /**
     * Get icon class for UI.
     */
    public function icon(): string
    {
        return match ($this) {
            self::ALLOWANCE_TYPES => 'bx bx-wallet',
            self::DOCUMENT_TYPES  => 'bx bx-file',
            self::VISA_TYPES      => 'bx bx-id-card',
        };
    }

    //======================
    /**
     * Get all values as an array.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    //======================
    /**
     * Get key-value array for dropdowns.
     *
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
