<?php

namespace Modules\AccessControl\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AccessControl\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'super_admin',
            'admin',
            'employee',
            'guardian',
            'student',
        ] as $roleName) {
            Role::findOrCreate($roleName, 'web');
        }
    }
}
