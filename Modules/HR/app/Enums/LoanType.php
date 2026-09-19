<?php

namespace Modules\HR\Enums;

enum LoanType: string
{
    case SALARY_ADVANCE   = 'salary_advance';
    case INSTALLMENT_LOAN = 'installment_loan';

    /**
     * Get the human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::SALARY_ADVANCE   => __('Salary Advance'),
            self::INSTALLMENT_LOAN => __('Installment Loan'),
        };
    }

    /**
     * Get the badge CSS class.
     */
    public function badgeClass(): string
    {
        return match ($this) {
            self::SALARY_ADVANCE   => 'bg-label-info',
            self::INSTALLMENT_LOAN => 'bg-label-primary',
        };
    }

    /**
     * Get the icon class.
     */
    public function icon(): string
    {
        return match ($this) {
            self::SALARY_ADVANCE   => 'bx bx-credit-card',
            self::INSTALLMENT_LOAN => 'bx bx-money-withdraw',
        };
    }
}
