<?php

namespace Modules\HR\Enums;

enum RejectionReason: string
{
    case EXPERIENCE_MISMATCH = 'experience_mismatch';
    case SALARY_MISMATCH     = 'salary_mismatch';
    case FAILED_ASSESSMENT   = 'failed_assessment';
    case FAILED_INTERVIEW    = 'failed_interview';
    case BETTER_CANDIDATE    = 'better_candidate';
    case OVERQUALIFIED       = 'overqualified';
    case LOCATION_COMMUTE    = 'location_commute';
    case OTHER               = 'other';

    public function label(): string
    {
        return match ($this) {
            self::EXPERIENCE_MISMATCH => __('Years of experience or qualifications not aligned'),
            self::SALARY_MISMATCH     => __('Salary expectations beyond budget'),
            self::FAILED_ASSESSMENT   => __('Did not pass technical / practical assessment'),
            self::FAILED_INTERVIEW    => __('Did not pass interview stage'),
            self::BETTER_CANDIDATE    => __('Another candidate was selected'),
            self::OVERQUALIFIED       => __('Overqualified for role specifications'),
            self::LOCATION_COMMUTE    => __('Location or commute constraints'),
            self::OTHER               => __('Other reasons'),
        };
    }
}
