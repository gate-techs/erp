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
        Schema::create('hr_job_requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('requisition_number', 50)->unique();
            $table->foreignId('workforce_plan_item_id')->nullable()->constrained('hr_workforce_plan_items')->nullOnDelete();
            $table->boolean('is_budgeted')->default(true);
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('nursery_branches')->nullOnDelete();
            $table->foreignId('job_title_id')->constrained('job_titles')->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->string('requisition_type', 30)->default('expansion'); // expansion, replacement, temporary, seasonal
            $table->foreignId('replacement_for_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->unsignedInteger('headcount_needed')->default(1);
            $table->unsignedInteger('hired_count')->default(0);
            $table->string('employment_type', 30)->default('full_time');
            $table->date('expected_start_date')->nullable();
            $table->decimal('min_salary', 10, 3)->nullable();
            $table->decimal('max_salary', 10, 3)->nullable();
            $table->string('currency', 10)->default('KWD');
            $table->unsignedSmallInteger('required_experience_years')->default(0);
            $table->string('education_level', 100)->nullable();
            $table->longText('job_description')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('requirements')->nullable();
            $table->text('justification')->nullable();
            $table->string('status', 30)->default('draft'); // draft, pending_approval, approved, rejected, in_recruitment, fulfilled, cancelled
            $table->string('priority', 20)->default('medium'); // low, medium, high, urgent
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'department_id']);
            $table->index(['is_budgeted', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_job_requisitions');
    }
};
