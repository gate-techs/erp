<?php

namespace Modules\HR\Enums;

enum PlanQuarter: string
{
    case Q1     = 'Q1';
    case Q2     = 'Q2';
    case Q3     = 'Q3';
    case Q4     = 'Q4';
    case ANNUAL = 'annual';

    public function label(): string
    {
        return match ($this) {
            self::Q1     => __('Q1 (Jan - Mar)'),
            self::Q2     => __('Q2 (Apr - Jun)'),
            self::Q3     => __('Q3 (Jul - Sep)'),
            self::Q4     => __('Q4 (Oct - Dec)'),
            self::ANNUAL => __('Full Year / Annual'),
        };
    }
}
