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
        Schema::create('hr_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->string('type')->default('installment_loan'); // LoanType enum
            $table->decimal('amount', 10, 2);
            $table->unsignedInteger('installments_count')->default(1);
            $table->decimal('monthly_installment', 10, 2);
            $table->date('start_date');
            $table->decimal('total_paid', 10, 2)->default(0.00);
            $table->decimal('remaining_amount', 10, 2);
            $table->string('status')->default('pending'); // LoanStatus enum

            $table->text('reason')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('attachment')->nullable();

            // Disbursement details
            $table->timestamp('disbursed_at')->nullable();
            $table->string('disbursement_method')->nullable(); // bank_transfer, cash, cheque
            $table->string('disbursement_reference')->nullable();

            // Approvals
            $table->foreignId('manager_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('manager_approved_at')->nullable();
            $table->foreignId('hr_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hr_approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'status']);
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_loans');
    }
};
