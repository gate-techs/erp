<?php

namespace Modules\Nursery\Enums;

enum DayOfWeek: string
{
    case SATURDAY = 'saturday';
    case SUNDAY = 'sunday';
    case MONDAY = 'monday';
    case TUESDAY = 'tuesday';
    case WEDNESDAY = 'wednesday';
    case THURSDAY = 'thursday';
    case FRIDAY = 'friday';

    //======================
    /**
     * Get human-readable label in Arabic / English.
     */
    public function label(): string
    {
        return match ($this) {
            self::SATURDAY => __('Saturday'),
            self::SUNDAY => __('Sunday'),
            self::MONDAY => __('Monday'),
            self::TUESDAY => __('Tuesday'),
            self::WEDNESDAY => __('Wednesday'),
            self::THURSDAY => __('Thursday'),
            self::FRIDAY => __('Friday'),
        };
    }

    //======================
    /**
     * Get all raw values.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    //======================
    /**
     * Get default schedule structure for a standard work week in Kuwait/Gulf.
     * (Sunday to Thursday work, Friday & Saturday weekend off).
     *
     * @return array<int, array{day: string, from_time: ?string, to_time: ?string, is_off: bool}>
     */
    public static function defaultSchedule(): array
    {
        return [
            ['day' => self::SUNDAY->value, 'from_time' => '07:00', 'to_time' => '14:00', 'is_off' => false],
            ['day' => self::MONDAY->value, 'from_time' => '07:00', 'to_time' => '14:00', 'is_off' => false],
            ['day' => self::TUESDAY->value, 'from_time' => '07:00', 'to_time' => '14:00', 'is_off' => false],
            ['day' => self::WEDNESDAY->value, 'from_time' => '07:00', 'to_time' => '14:00', 'is_off' => false],
            ['day' => self::THURSDAY->value, 'from_time' => '07:00', 'to_time' => '14:00', 'is_off' => false],
            ['day' => self::FRIDAY->value, 'from_time' => null, 'to_time' => null, 'is_off' => true],
            ['day' => self::SATURDAY->value, 'from_time' => null, 'to_time' => null, 'is_off' => true],
        ];
    }
}
