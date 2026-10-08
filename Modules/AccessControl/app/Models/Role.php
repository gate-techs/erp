<?php

namespace Modules\AccessControl\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $guarded = [];

    /**
     * Core system roles that cannot be deleted.
     */
    public const SYSTEM_ROLES = [
        'super_admin',
        'admin',
        'supervisor',
        'teacher',
        'nurse',
        'employee',
        'guardian',
        'student',
        'hr_manager',
        'accountant_manager',
        'head_office',
    ];

    /**
     * Determine if this is a protected system role.
     */
    public function isSystem(): bool
    {
        return in_array(strtolower(trim($this->name)), self::SYSTEM_ROLES);
    }

    /**
     * Get human-readable translated name for the role.
     */
    public function getDisplayNameAttribute(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && !empty($this->name_ar)) {
            return $this->name_ar;
        }
        if ($locale !== 'ar' && !empty($this->name_en)) {
            return $this->name_en;
        }
        if (!empty($this->name_ar)) {
            return $this->name_ar;
        }
        if (!empty($this->name_en)) {
            return $this->name_en;
        }

        $key = strtolower(trim($this->name));
        $map = [
            'super_admin'        => __('Super Administrator'),
            'admin'              => __('System Administrator'),
            'supervisor'         => __('Academic Supervisor'),
            'teacher'            => __('Academic Teacher'),
            'nurse'              => __('Nurse'),
            'employee'           => __('Administrative Employee'),
            'guardian'           => __('Guardian'),
            'student'            => __('Student'),
            'hr_manager'         => __('HR Manager'),
            'accountant_manager' => __('Accounting Manager'),
            'head_office'        => __('Head Office'),
        ];


        return $map[$key] ?? __($this->name);
    }
}
