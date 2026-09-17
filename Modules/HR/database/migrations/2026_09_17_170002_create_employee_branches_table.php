<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //======================
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('nursery_branches')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['employee_id', 'branch_id'], 'emp_branches_emp_branch_unique');
        });
    }

    //======================
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_branches');
    }
};
