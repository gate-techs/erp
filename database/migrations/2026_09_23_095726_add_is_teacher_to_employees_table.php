<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'is_teacher')) {
                $table->boolean('is_teacher')->default(false)->after('employee_code')->index();
            }
            if (Schema::hasColumn('employees', 'staff_type')) {
                $table->dropColumn('staff_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'is_teacher')) {
                $table->dropColumn('is_teacher');
            }
        });
    }
};
