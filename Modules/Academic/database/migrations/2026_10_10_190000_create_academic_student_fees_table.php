<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_student_id')->constrained('academic_students')->cascadeOnDelete();
            $table->foreignId('academic_fee_id')->constrained('academic_fees')->restrictOnDelete();
            $table->decimal('total_amount', 12, 2);
            $table->unsignedInteger('installments_count')->default(1);
            $table->decimal('installment_amount', 12, 2);
            $table->date('start_date');
            $table->string('status', 20)->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['academic_student_id', 'academic_fee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_student_fees');
    }
};
