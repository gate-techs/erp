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
        Schema::create('hr_payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('hr_payrolls')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();

            // Earnings (الاستحقاقات)
            $table->decimal('basic_salary', 10, 2);
            $table->decimal('allowances_amount', 10, 2)->default(0.00);
            $table->json('allowances_breakdown')->nullable();

            $table->decimal('overtime_hours', 6, 2)->default(0.00);
            $table->decimal('overtime_amount', 10, 2)->default(0.00);

            $table->decimal('rewards_amount', 10, 2)->default(0.00);
            $table->decimal('other_earnings', 10, 2)->default(0.00);

            $table->decimal('gross_salary', 10, 2)->default(0.00);

            // Deductions (الاستقطاعات)
            $table->decimal('absence_days', 5, 2)->default(0.00);
            $table->decimal('absence_deduction', 10, 2)->default(0.00);

            $table->unsignedInteger('late_minutes')->default(0);
            $table->decimal('late_deduction', 10, 2)->default(0.00);

            $table->decimal('loans_deduction', 10, 2)->default(0.00);
            $table->decimal('penalties_deduction', 10, 2)->default(0.00);
            $table->decimal('insurance_deduction', 10, 2)->default(0.00);
            $table->decimal('other_deductions', 10, 2)->default(0.00);

            $table->decimal('total_deductions', 10, 2)->default(0.00);

            // Net Salary (صافي الراتب)
            $table->decimal('net_salary', 10, 2)->default(0.00);

            // Bank / WPS details snapshot
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('iban')->nullable();

            // Status
            $table->string('payment_status')->default('pending'); // PayrollPaymentStatus
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes & Unique constraints
            $table->unique(['payroll_id', 'employee_id']);
            $table->index(['employee_id', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_payroll_items');
    }
};
