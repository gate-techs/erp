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
        Schema::create('hr_workforce_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workforce_plan_id')->constrained('hr_workforce_plans')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('nursery_branches')->nullOnDelete();
            $table->foreignId('job_title_id')->constrained('job_titles')->cascadeOnDelete();
            $table->string('employment_type', 30)->default('full_time');
            $table->unsignedInteger('current_headcount')->default(0);
            $table->unsignedInteger('target_headcount')->default(1);
            $table->integer('vacancies_count')->default(1); // can be negative if overstaffed
            $table->unsignedInteger('fulfilled_count')->default(0);
            $table->decimal('estimated_monthly_salary', 10, 3)->default(0);
            $table->decimal('estimated_total_cost', 14, 3)->default(0);
            $table->string('priority', 20)->default('medium'); // low, medium, high, critical
            $table->string('target_quarter', 15)->default('annual'); // Q1, Q2, Q3, Q4, annual
            $table->text('justification')->nullable();
            $table->timestamps();

            $table->index(['workforce_plan_id', 'department_id']);
            $table->index(['department_id', 'job_title_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_workforce_plan_items');
    }
};
