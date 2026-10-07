<?php

namespace Modules\Settings\Enums;

enum NotificationType: string
{
    case LEAVE_SUBMITTED = 'leave_submitted';
    case LEAVE_APPROVED = 'leave_approved';
    case LEAVE_REJECTED = 'leave_rejected';
    case TASK_ASSIGNED = 'task_assigned';
    case TASK_NOTE_ADDED = 'task_note_added';

    public function label(): string
    {
        return match ($this) {
            self::LEAVE_SUBMITTED => __('Leave request submitted'),
            self::LEAVE_APPROVED => __('Leave request approved'),
            self::LEAVE_REJECTED => __('Leave request rejected'),
            self::TASK_ASSIGNED => __('Task assigned'),
            self::TASK_NOTE_ADDED => __('Task note added'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function toArray(): array
    {
        $result = [];
        foreach (self::cases() as $case) {
            $result[$case->value] = $case->label();
        }

        return $result;
    }
}
