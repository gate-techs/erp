<?php

namespace Modules\AccessControl\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\AccessControl\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super_admin'        => ['ar' => 'السوبر ادمن', 'en' => 'Super Administrator'],
            'admin'              => ['ar' => 'مدير النظام', 'en' => 'System Administrator'],
            'employee'           => ['ar' => 'موظف إداري', 'en' => 'Administrative Employee'],
            'guardian'           => ['ar' => 'ولي أمر', 'en' => 'Guardian'],
            'student'            => ['ar' => 'طالب', 'en' => 'Student'],
            'teacher'            => ['ar' => 'معلم', 'en' => 'Teacher'],
            'supervisor'         => ['ar' => 'مشرف أكاديمي', 'en' => 'Supervisor'],
            'nurse'              => ['ar' => 'ممرض', 'en' => 'Nurse'],
            'hr_manager'         => ['ar' => 'مدير الموارد البشرية', 'en' => 'HR Manager'],
            'accountant_manager' => ['ar' => 'مدير الحسابات', 'en' => 'Accounting Manager'],
            'head_office'        => ['ar' => 'الإدارة العامة ', 'en' => 'Head Office'],
        ];


        foreach ($roles as $roleName => $meta) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->update([
                'name_ar' => $meta['ar'],
                'name_en' => $meta['en'],
            ]);
        }
    }

}
