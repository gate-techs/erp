<?php

namespace Modules\HR\Enums;

enum AttendanceStatus: string
{
    case PRESENT = 'present';
    case ABSENT = 'absent';
    case LATE = 'late';
    case HALF_DAY = 'half_day';
    case ON_LEAVE = 'on_leave';
    case HOLIDAY = 'holiday';
    case REST_DAY = 'rest_day';
    case PENDING = 'pending';
    case FUTURE = 'future';

    //======================
    /**
     * Get human-readable label for status.
     */
    public function label(): string
    {
        return match ($this) {
            self::PRESENT => __('Present'),
            self::ABSENT => __('Absent'),
            self::LATE => __('Late'),
            self::HALF_DAY => __('Half Day'),
            self::ON_LEAVE => __('Leave'),
            self::HOLIDAY => __('Holiday'),
            self::REST_DAY => __('Rest Day'),
            self::PENDING => __('Pending'),
            self::FUTURE => __('Future'),
        };
    }

    //======================
    /**
     * Get badge color class for UI.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::PRESENT => 'bg-label-success text-success',
            self::ABSENT => 'bg-label-danger text-danger',
            self::LATE => 'bg-label-warning text-warning',
            self::HALF_DAY => 'bg-label-info text-info',
            self::ON_LEAVE => 'bg-label-primary text-primary',
            self::HOLIDAY => 'bg-label-secondary text-secondary',
            self::REST_DAY => 'bg-label-dark text-muted',
            self::PENDING => 'bg-label-warning text-warning',
            self::FUTURE => 'bg-light text-muted',
        };
    }

    //======================
    /**
     * Get icon representation for matrix grid.
     */
    public function icon(): string
    {
        return match ($this) {
            self::PRESENT => '✓',
            self::ABSENT => '✕',
            self::LATE => '🕒',
            self::HALF_DAY => '½',
            self::ON_LEAVE => '🚩',
            self::HOLIDAY => '⭐',
            self::REST_DAY => '⃠',
            self::PENDING => '⏳',
            self::FUTURE => '-',
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
