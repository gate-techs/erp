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
        Schema::create('hr_job_openings', function (Blueprint $table) {
            $table->id();
            $table->string('opening_code', 50)->unique();
            $table->foreignId('job_requisition_id')->nullable()->constrained('hr_job_requisitions')->nullOnDelete();
            $table->string('title');
            $table->foreignId('job_title_id')->constrained('job_titles')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('nursery_branches')->nullOnDelete();
            $table->string('employment_type', 50)->default('full_time');
            $table->string('workplace_type', 50)->default('on_site');
            $table->string('posting_type', 50)->default('both');
            $table->unsignedInteger('open_positions')->default(1);
            $table->unsignedInteger('filled_positions')->default(0);
            $table->unsignedInteger('min_experience_years')->nullable();
            $table->unsignedInteger('max_experience_years')->nullable();
            $table->string('education_level')->nullable();
            $table->decimal('min_salary', 12, 3)->nullable();
            $table->decimal('max_salary', 12, 3)->nullable();
            $table->string('currency', 10)->default('KWD');
            $table->boolean('hide_salary')->default(false);
            $table->date('publish_date')->nullable();
            $table->date('closing_date')->nullable();
            $table->text('job_summary')->nullable();
            $table->longText('responsibilities')->nullable();
            $table->longText('requirements')->nullable();
            $table->longText('benefits')->nullable();
            $table->string('status', 50)->default('draft');
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('applications_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_job_openings');
    }
};
