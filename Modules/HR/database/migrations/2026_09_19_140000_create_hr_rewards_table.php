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
        Schema::create('hr_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('reward_number')->unique();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('reward_type_id')->constrained('hr_settings')->cascadeOnDelete();

            $table->string('calculation_type')->default('fixed_amount'); // RewardCalculationType
            $table->decimal('calculation_value', 10, 2);
            $table->decimal('amount', 10, 2);
            $table->date('award_date');

            $table->string('payment_method')->default('next_payroll'); // RewardPaymentMethod
            $table->string('payroll_month', 7)->nullable(); // YYYY-MM
            $table->string('status')->default('pending'); // RewardStatus

            $table->text('reason');
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();
            $table->text('rejection_reason')->nullable();

            // Approval & Payment Lifecycle
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();

            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('rejected_at')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['employee_id', 'status']);
            $table->index(['reward_type_id', 'status']);
            $table->index('award_date');
            $table->index('payroll_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_rewards');
    }
};
