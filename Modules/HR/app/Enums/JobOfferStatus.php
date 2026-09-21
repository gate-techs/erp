<?php

namespace Modules\HR\Enums;

enum JobOfferStatus: string
{
    case DRAFT            = 'draft';
    case PENDING_APPROVAL = 'pending_approval';
    case APPROVED         = 'approved';
    case SENT             = 'sent';
    case ACCEPTED         = 'accepted';
    case DECLINED         = 'declined';
    case CANCELLED        = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT            => __('Draft'),
            self::PENDING_APPROVAL => __('Pending Approval'),
            self::APPROVED         => __('Approved'),
            self::SENT             => __('Sent to Candidate'),
            self::ACCEPTED         => __('Accepted'),
            self::DECLINED         => __('Declined'),
            self::CANCELLED        => __('Cancelled'),
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::DRAFT            => 'bg-label-secondary',
            self::PENDING_APPROVAL => 'bg-label-warning',
            self::APPROVED         => 'bg-label-info',
            self::SENT             => 'bg-label-primary',
            self::ACCEPTED         => 'bg-label-success',
            self::DECLINED         => 'bg-label-danger',
            self::CANCELLED        => 'bg-label-dark',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::DRAFT            => 'bx bx-edit-alt',
            self::PENDING_APPROVAL => 'bx bx-time-five',
            self::APPROVED         => 'bx bx-check-shield',
            self::SENT             => 'bx bx-send',
            self::ACCEPTED         => 'bx bx-check-circle',
            self::DECLINED         => 'bx bx-x-circle',
            self::CANCELLED        => 'bx bx-block',
        };
    }
}
