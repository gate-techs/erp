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
        Schema::create('hr_payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('payroll_number')->unique();
            $table->string('payroll_month', 7); // YYYY-MM
            $table->string('title')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('nursery_branches')->nullOnDelete();

            // Aggregated Financial Totals
            $table->decimal('total_basic_salary', 12, 2)->default(0.00);
            $table->decimal('total_allowances', 12, 2)->default(0.00);
            $table->decimal('total_overtime', 12, 2)->default(0.00);
            $table->decimal('total_rewards', 12, 2)->default(0.00);
            $table->decimal('total_gross', 12, 2)->default(0.00);

            $table->decimal('total_deductions', 12, 2)->default(0.00);
            $table->decimal('total_loans_deduction', 12, 2)->default(0.00);
            $table->decimal('total_penalties_deduction', 12, 2)->default(0.00);
            $table->decimal('total_absence_deduction', 12, 2)->default(0.00);

            $table->decimal('total_net', 12, 2)->default(0.00);
            $table->unsignedInteger('employees_count')->default(0);

            // Lifecycle Status: draft, approved, paid, cancelled
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();

            // Audit & Approval Trail
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();

            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('payroll_month');
            $table->index('status');
            $table->index(['payroll_month', 'status']);
            $table->index('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_payrolls');
    }
};
