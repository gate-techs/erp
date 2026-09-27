<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'name_ar')) {
                $table->string('name_ar')->nullable()->after('name');
            }
            if (!Schema::hasColumn('roles', 'name_en')) {
                $table->string('name_en')->nullable()->after('name_ar');
            }
        });

        // Set default Arabic and English names for standard system roles
        $defaults = [
            'super_admin' => ['name_ar' => 'المدير الأعلى', 'name_en' => 'Super Administrator'],
            'admin'       => ['name_ar' => 'مدير النظام', 'name_en' => 'System Administrator'],
            'supervisor'  => ['name_ar' => 'مشرف أكاديمي', 'name_en' => 'Academic Supervisor'],
            'teacher'     => ['name_ar' => 'معلم', 'name_en' => 'Teacher'],
            'nurse'       => ['name_ar' => 'ممرض', 'name_en' => 'Nurse'],
            'employee'    => ['name_ar' => 'موظف إداري', 'name_en' => 'Administrative Employee'],
            'guardian'    => ['name_ar' => 'ولي أمر', 'name_en' => 'Guardian'],
            'student'     => ['name_ar' => 'طالب', 'name_en' => 'Student'],
            'محاسب'       => ['name_ar' => 'محاسب', 'name_en' => 'Accountant'],
        ];

        foreach ($defaults as $slug => $names) {
            DB::table('roles')->where('name', $slug)->update($names);
        }

        // Fill any other existing roles that didn't have names
        $otherRoles = DB::table('roles')->whereNull('name_ar')->orWhereNull('name_en')->get();
        foreach ($otherRoles as $r) {
            DB::table('roles')->where('id', $r->id)->update([
                'name_ar' => $r->name_ar ?: $r->name,
                'name_en' => $r->name_en ?: $r->name,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'name_ar')) {
                $table->dropColumn('name_ar');
            }
            if (Schema::hasColumn('roles', 'name_en')) {
                $table->dropColumn('name_en');
            }
        });
    }
};
