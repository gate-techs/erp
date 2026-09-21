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
        // 1. Interviews Sessions Table
        Schema::create('hr_interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained('hr_job_applications')->cascadeOnDelete();
            $table->foreignId('job_opening_id')->nullable()->constrained('hr_job_openings')->nullOnDelete();
            $table->foreignId('candidate_id')->nullable()->constrained('hr_candidates')->nullOnDelete();

            $table->integer('interview_round')->default(1);
            $table->string('title')->nullable(); // e.g. "المقابلة الفنية الأولى"
            $table->string('type')->default('technical'); // InterviewType
            $table->string('mode')->default('online'); // InterviewMode
            $table->string('status')->default('scheduled'); // InterviewStatus

            $table->dateTime('scheduled_at');
            $table->integer('duration_minutes')->default(45);
            $table->string('location')->nullable(); // For in_person
            $table->string('meeting_link', 500)->nullable(); // For online (Zoom/Meet/Teams)
            $table->text('notes')->nullable();

            $table->decimal('overall_score', 3, 2)->nullable(); // 1.00 to 5.00 calculated
            $table->string('final_recommendation')->nullable(); // EvaluationRecommendation

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['job_application_id', 'status']);
            $table->index('scheduled_at');
        });

        // 2. Interviewers Pivot Table (Staff / Employees participating in the interview)
        Schema::create('hr_interview_interviewers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained('hr_interviews')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['interview_id', 'employee_id']);
        });

        // 3. Interview Scorecard / Evaluations Table
        Schema::create('hr_interview_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained('hr_interviews')->cascadeOnDelete();
            $table->foreignId('interviewer_id')->constrained('employees')->cascadeOnDelete();

            // Rubric Criteria (1 - 5 stars/points)
            $table->unsignedTinyInteger('technical_score')->nullable();
            $table->unsignedTinyInteger('communication_score')->nullable();
            $table->unsignedTinyInteger('problem_solving_score')->nullable();
            $table->unsignedTinyInteger('cultural_fit_score')->nullable();
            $table->unsignedTinyInteger('experience_score')->nullable();

            $table->decimal('average_score', 3, 2)->default(0.00);
            $table->string('recommendation')->default('neutral'); // EvaluationRecommendation

            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('notes')->nullable();

            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['interview_id', 'interviewer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_interview_evaluations');
        Schema::dropIfExists('hr_interview_interviewers');
        Schema::dropIfExists('hr_interviews');
    }
};
