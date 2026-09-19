<?php

namespace Modules\HR\Enums;

enum JobRequisitionStatus: string
{
    case DRAFT            = 'draft';
    case PENDING_APPROVAL = 'pending_approval';
    case APPROVED         = 'approved';
    case REJECTED         = 'rejected';
    case IN_RECRUITMENT   = 'in_recruitment';
    case FULFILLED        = 'fulfilled';
    case CANCELLED        = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT            => __('Draft'),
            self::PENDING_APPROVAL => __('Pending Approval'),
            self::APPROVED         => __('Approved'),
            self::REJECTED         => __('Rejected'),
            self::IN_RECRUITMENT   => __('In Recruitment / Sourcing'),
            self::FULFILLED        => __('Fulfilled / Hired'),
            self::CANCELLED        => __('Cancelled'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT            => 'bg-label-secondary',
            self::PENDING_APPROVAL => 'bg-label-warning',
            self::APPROVED         => 'bg-label-info',
            self::REJECTED         => 'bg-label-danger',
            self::IN_RECRUITMENT   => 'bg-label-primary',
            self::FULFILLED        => 'bg-label-success',
            self::CANCELLED        => 'bg-label-dark',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::DRAFT            => 'bx bx-edit',
            self::PENDING_APPROVAL => 'bx bx-time-five',
            self::APPROVED         => 'bx bx-check',
            self::REJECTED         => 'bx bx-x',
            self::IN_RECRUITMENT   => 'bx bx-user-voice',
            self::FULFILLED        => 'bx bx-check-double',
            self::CANCELLED        => 'bx bx-block',
        };
    }
}
