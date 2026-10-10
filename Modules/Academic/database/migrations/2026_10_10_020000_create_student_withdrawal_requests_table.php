<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_withdrawal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('nurseries')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('nursery_branches')->cascadeOnDelete();
            $table->foreignId('academic_student_id')->constrained('academic_students')->cascadeOnDelete();
            $table->foreignId('academic_class_id')->nullable()->constrained('academic_classes')->nullOnDelete();
            $table->foreignId('academic_guardian_id')->nullable()->constrained('academic_guardians')->nullOnDelete();
            $table->string('civil_id_snapshot')->nullable();
            $table->date('request_date');
            $table->date('effective_withdrawal_date')->nullable();
            $table->text('reason');
            $table->longText('parent_signature')->nullable();
            $table->longText('branch_manager_signature')->nullable();
            $table->text('parent_financial_obligations')->nullable();
            $table->text('management_report')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['academic_student_id', 'status']);
            $table->index(['branch_id', 'request_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_withdrawal_requests');
    }
};
