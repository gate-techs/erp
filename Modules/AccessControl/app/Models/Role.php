<?php

namespace Modules\AccessControl\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    protected $guarded = [];

    /**
     * Get human-readable translated name for the role.
     */
    public function getDisplayNameAttribute(): string
    {
        $key = strtolower(trim($this->name));
        $map = [
            'super_admin' => __('Super Administrator'),
            'admin' => __('System Administrator'),
            'employee' => __('Employee'),
            'guardian' => __('Guardian'),
            'student' => __('Student'),
        ];

        return $map[$key] ?? __($this->name);
    }
}
