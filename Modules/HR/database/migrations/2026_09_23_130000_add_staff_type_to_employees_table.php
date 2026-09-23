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
        if (!Schema::hasColumn('employees', 'staff_type')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->string('staff_type', 50)->default('employee')->after('employee_code');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('employees', 'staff_type')) {
            Schema::table('employees', function (Blueprint $table) {
                $table->dropColumn('staff_type');
            });
        }
    }
};
