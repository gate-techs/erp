<?php

namespace App\Enums;

enum UserType: string
{
    case ADMIN = 'admin';
    case EMPLOYEE = 'employee';
    case GUARDIAN = 'guardian';
    case STUDENT = 'student';
    case DEVELOPER = 'developer';

    /**
     * Get the display label for the user type.
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMIN => __('Admin'),
            self::EMPLOYEE => __('Employee'),
            self::GUARDIAN => __('Guardian'),
            self::STUDENT => __('Student'),
            self::DEVELOPER => __('Developer'),
        };
    }

    /**
     * Get all raw values.
     *
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isDeveloper(): bool
    {
        return $this === self::DEVELOPER;
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public function isEmployee(): bool
    {
        return $this === self::EMPLOYEE;
    }

    public function isGuardian(): bool
    {
        return $this === self::GUARDIAN;
    }

    public function isStudent(): bool
    {
        return $this === self::STUDENT;
    }
}
