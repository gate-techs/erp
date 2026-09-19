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
        Schema::create('hr_penalties', function (Blueprint $table) {
            $table->id();
            $table->string('penalty_number')->unique();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('penalty_type_id')->constrained('hr_settings')->cascadeOnDelete();

            $table->string('calculation_type')->default('days_deduction'); // PenaltyCalculationType
            $table->decimal('calculation_value', 10, 2)->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->date('penalty_date');

            $table->string('deduction_method')->default('next_payroll'); // PenaltyDeductionMethod
            $table->string('payroll_month', 7)->nullable(); // YYYY-MM
            $table->string('status')->default('pending'); // PenaltyStatus

            $table->text('reason');
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Approval, Deduction & Cancellation Lifecycle
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->foreignId('deducted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('deducted_at')->nullable();

            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['employee_id', 'status']);
            $table->index(['penalty_type_id', 'status']);
            $table->index('penalty_date');
            $table->index('payroll_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_penalties');
    }
};
