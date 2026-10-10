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
        Schema::create('academic_student_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_student_id')->constrained('academic_students')->cascadeOnDelete();
            $table->foreignId('academic_class_id')->constrained('academic_classes')->cascadeOnDelete();
            $table->foreignId('institution_id')->nullable()->constrained('nurseries')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('nursery_branches')->nullOnDelete();

            $table->date('date')->index();
            $table->tinyInteger('status')->default(1)->comment('1=Attend, 0=Absent, 2=Late, 3=Excused');
            $table->boolean('approved')->default(false)->index()->comment('Approval status by admin');

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Unique constraint: A student can only have 1 attendance record per date
            $table->unique(['academic_student_id', 'date'], 'student_date_unique');
            $table->index(['academic_class_id', 'date'], 'class_date_index');
            $table->index(['date', 'status'], 'date_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_student_attendances');
    }
};
