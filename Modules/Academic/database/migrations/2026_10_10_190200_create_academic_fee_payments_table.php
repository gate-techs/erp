<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_student_fee_id')->constrained('academic_student_fees')->cascadeOnDelete();
            $table->foreignId('academic_fee_installment_id')->nullable()->constrained('academic_fee_installments')->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('invoice_number', 100)->nullable()->index();
            $table->string('payment_number', 100)->unique();
            $table->string('transaction_number', 150)->nullable()->index();
            $table->string('reference_number', 150)->nullable()->index();
            $table->string('tracking_number', 150)->nullable()->index();
            $table->string('status', 20)->default('paid')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_fee_payments');
    }
};
