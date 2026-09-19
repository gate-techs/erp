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
        Schema::create('hr_loan_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('hr_loans')->cascadeOnDelete();
            $table->unsignedInteger('installment_number');
            $table->date('due_date');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0.00);
            $table->string('status')->default('pending'); // InstallmentStatus enum

            $table->date('paid_at')->nullable();
            $table->date('postponed_from')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('payroll_id')->nullable(); // For future payroll deduction integration

            $table->timestamps();

            $table->index(['loan_id', 'status']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_loan_installments');
    }
};
