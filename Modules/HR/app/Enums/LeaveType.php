<?php

namespace Modules\HR\Enums;

enum LeaveType: string
{
    case ANNUAL = 'annual';
    case CASUAL = 'casual';
    case SICK = 'sick';
    case MATERNITY = 'maternity';
    case HAJJ = 'hajj';
    case MARRIAGE = 'marriage';
    case BEREAVEMENT = 'bereavement';
    case PATERNITY = 'paternity';
    case UNPAID = 'unpaid';

    //======================
    /**
     * Get human-readable label for leave type.
     */
    public function label(): string
    {
        return match ($this) {
            self::ANNUAL      => __('Annual Leave'),
            self::CASUAL      => __('Casual Leave'),
            self::SICK        => __('Sick Leave'),
            self::MATERNITY   => __('Maternity Leave'),
            self::HAJJ        => __('Hajj Leave'),
            self::MARRIAGE    => __('Marriage Leave'),
            self::BEREAVEMENT => __('Bereavement Leave'),
            self::PATERNITY   => __('Paternity Leave'),
            self::UNPAID      => __('Unpaid Leave'),
        };
    }

    //======================
    /**
     * Get CSS badge class for UI representation.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::ANNUAL      => 'bg-label-primary text-primary',
            self::CASUAL      => 'bg-label-info text-info',
            self::SICK        => 'bg-label-warning text-warning',
            self::MATERNITY   => 'bg-label-danger text-danger',
            self::HAJJ        => 'bg-label-success text-success',
            self::MARRIAGE    => 'bg-label-purple text-purple',
            self::BEREAVEMENT => 'bg-label-dark text-dark',
            self::PATERNITY   => 'bg-label-secondary text-secondary',
            self::UNPAID      => 'bg-light text-muted',
        };
    }

    //======================
    /**
     * Get icon class for UI representation.
     */
    public function icon(): string
    {
        return match ($this) {
            self::ANNUAL      => 'bx bx-sun',
            self::CASUAL      => 'bx bx-bolt-circle',
            self::SICK        => 'bx bx-plus-medical',
            self::MATERNITY   => 'bx bx-heart',
            self::HAJJ        => 'bx bx-trip',
            self::MARRIAGE    => 'bx bx-party',
            self::BEREAVEMENT => 'bx bx-sad',
            self::PATERNITY   => 'bx bx-child',
            self::UNPAID      => 'bx bx-money-withdraw',
        };
    }

    //======================
    /**
     * Determine whether attachment is typically required/expected for this type.
     */
    public function requiresAttachment(): bool
    {
        return match ($this) {
            self::SICK, self::HAJJ, self::MATERNITY => true,
            default                                 => false,
        };
    }

    //======================
    /**
     * Key in hr_work_policies defining annual balance.
     */
    public function policyDaysKey(): ?string
    {
        return match ($this) {
            self::ANNUAL      => 'annual_leave_days',
            self::CASUAL      => 'casual_leave_annual_days',
            self::MATERNITY   => 'maternity_leave_days',
            self::HAJJ        => 'hajj_leave_days',
            self::MARRIAGE    => 'marriage_leave_days',
            self::BEREAVEMENT => 'bereavement_leave_days',
            self::PATERNITY   => 'paternity_leave_days',
            default           => null,
        };
    }

    //======================
    /**
     * Return all enum cases as array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
