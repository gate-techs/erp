<?php

namespace Modules\Settings\Enums;

enum GeneralSettingType: string
{
    case GOVERNMENT = 'government';
    case CITY = 'city';
    case NATIONALITY = 'nationality';
    case RELIGION = 'religion';
    case BLOOD_TYPE = 'blood_type';
    case GENDER = 'gender';
    case EDUCATION_LEVEL = 'education_level';
    case RELATIONSHIP = 'relationship';
    case MARITAL_STATUS = 'marital_status';
    case OTHER = 'other';

    /**
     * Get the human-readable translated label for the type.
     */
    public function label(): string
    {
        return match ($this) {
            self::GOVERNMENT => __('Government / Governorate'),
            self::CITY => __('City / Area'),
            self::NATIONALITY => __('Nationality'),
            self::RELIGION => __('Religion'),
            self::BLOOD_TYPE => __('Blood Type'),
            self::GENDER => __('Gender'),
            self::EDUCATION_LEVEL => __('Education Level'),
            self::RELATIONSHIP => __('Relationship'),
            self::MARITAL_STATUS => __('Marital Status'),
            self::OTHER => __('Other'),
        };
    }

    /**
     * Get all enum string values.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get an associative array suitable for dropdown selects [value => label].
     *
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->label();
        }

        return $result;
    }
}
