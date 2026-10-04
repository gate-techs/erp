<?php

namespace Modules\Tasks\Enums;

enum TaskStatus: string
{
    case PENDING     = 'pending';
    case IN_PROGRESS = 'in_progress';
    case ON_HOLD     = 'on_hold';
    case COMPLETED   = 'completed';
    case CANCELLED   = 'cancelled';

    //-----------------------------------------
    /**
     * Get the display label for the status.
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING     => __('Pending'),
            self::IN_PROGRESS => __('In Progress'),
            self::ON_HOLD     => __('On Hold'),
            self::COMPLETED   => __('Completed'),
            self::CANCELLED   => __('Cancelled'),
        };
    }

    //-----------------------------------------
    /**
     * Get the badge color class for Sneat / Bootstrap 5.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::PENDING     => 'bg-label-warning',
            self::IN_PROGRESS => 'bg-label-primary',
            self::ON_HOLD     => 'bg-label-secondary',
            self::COMPLETED   => 'bg-label-success',
            self::CANCELLED   => 'bg-label-danger',
        };
    }

    //-----------------------------------------
    /**
     * Get hex color for UI badges, kanban cards, or charts.
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING     => '#ffab00',
            self::IN_PROGRESS => '#696cff',
            self::ON_HOLD     => '#8592a3',
            self::COMPLETED   => '#71dd37',
            self::CANCELLED   => '#ff3e1d',
        };
    }

    //-----------------------------------------
    /**
     * Get all raw values.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
