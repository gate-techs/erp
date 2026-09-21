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
        // 1. Candidates Table
        Schema::create('hr_candidates', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('full_name')->index();
            $table->string('email')->index();
            $table->string('phone', 30)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('current_job_title')->nullable();
            $table->string('current_company')->nullable();
            $table->decimal('total_experience_years', 4, 1)->nullable();
            $table->string('linkedin_url', 500)->nullable();
            $table->string('portfolio_url', 500)->nullable();
            $table->string('resume_path', 500)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Job Applications Table (Pipeline)
        Schema::create('hr_job_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_code', 30)->unique();
            $table->foreignId('candidate_id')->constrained('hr_candidates')->cascadeOnDelete();
            $table->foreignId('job_opening_id')->constrained('hr_job_openings')->cascadeOnDelete();
            $table->string('stage', 30)->default('new')->index();
            $table->string('status', 30)->default('active')->index();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->decimal('expected_salary', 12, 2)->nullable();
            $table->string('currency', 10)->default('KWD');
            $table->unsignedSmallInteger('notice_period_days')->nullable();
            $table->string('source', 50)->nullable();
            $table->text('cover_letter')->nullable();
            $table->string('rejection_reason', 50)->nullable();
            $table->text('rejection_notes')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('hired_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['job_opening_id', 'stage']);
            $table->index(['job_opening_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_job_applications');
        Schema::dropIfExists('hr_candidates');
    }
};
