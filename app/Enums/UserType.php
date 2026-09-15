<?php

namespace App\Enums;

enum UserType: string
{
    case ADMIN = 'admin';
    case EMPLOYEE = 'employee';
    case GUARDIAN = 'guardian';
    case STUDENT = 'student';

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
