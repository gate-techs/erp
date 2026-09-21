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
        // 1. Onboarding Journeys Table
        Schema::create('hr_onboardings', function (Blueprint $table) {
            $table->id();
            $table->string('onboarding_code')->unique(); // e.g. ONB-2026-0001
            $table->foreignId('candidate_id')->constrained('hr_candidates')->cascadeOnDelete();
            $table->foreignId('job_application_id')->nullable()->constrained('hr_job_applications')->nullOnDelete();
            $table->foreignId('job_offer_id')->nullable()->constrained('hr_job_offers')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete(); // Populated once converted

            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('job_title_id')->nullable()->constrained('job_titles')->nullOnDelete();
            $table->foreignId('buddy_employee_id')->nullable()->constrained('employees')->nullOnDelete();

            $table->date('start_date');
            $table->string('status')->default('pending'); // OnboardingStatus: pending, in_progress, completed, cancelled
            $table->unsignedTinyInteger('progress_percentage')->default(0);
            $table->text('notes')->nullable();
            $table->dateTime('completed_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'start_date']);
        });

        // 2. Onboarding Tasks / Checklist Items Table
        Schema::create('hr_onboarding_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('onboarding_id')->constrained('hr_onboardings')->cascadeOnDelete();
            $table->string('title');
            $table->string('category')->default('documentation'); // documentation, it_access, orientation, facilities
            $table->boolean('is_completed')->default(false);
            $table->dateTime('completed_at')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['onboarding_id', 'is_completed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_onboarding_tasks');
        Schema::dropIfExists('hr_onboardings');
    }
};
