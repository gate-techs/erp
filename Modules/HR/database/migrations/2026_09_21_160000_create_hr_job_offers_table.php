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
        Schema::create('hr_job_offers', function (Blueprint $table) {
            $table->id();
            $table->string('offer_code')->unique(); // e.g. OFF-2026-0001
            $table->foreignId('job_application_id')->constrained('hr_job_applications')->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained('hr_candidates')->cascadeOnDelete();
            $table->foreignId('job_opening_id')->nullable()->constrained('hr_job_openings')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('job_title_id')->nullable()->constrained('job_titles')->nullOnDelete();

            $table->string('employment_type')->default('full_time'); // full_time, part_time, contract, remote
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('housing_allowance', 12, 2)->default(0);
            $table->decimal('transportation_allowance', 12, 2)->default(0);
            $table->decimal('other_allowances', 12, 2)->default(0);
            $table->decimal('total_salary', 12, 2)->default(0);
            $table->string('currency', 10)->default('KWD');

            $table->date('offer_date');
            $table->date('expiry_date')->nullable();
            $table->date('joining_date')->nullable();
            $table->unsignedSmallInteger('probation_months')->default(3);
            $table->unsignedSmallInteger('contract_duration_months')->nullable(); // e.g. 12 or 24

            $table->string('status')->default('draft'); // JobOfferStatus: draft, pending_approval, approved, sent, accepted, declined, cancelled
            $table->text('terms_and_conditions')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('responded_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['candidate_id', 'status']);
            $table->index('offer_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_job_offers');
    }
};
