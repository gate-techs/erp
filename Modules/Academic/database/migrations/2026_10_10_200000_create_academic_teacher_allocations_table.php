<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //------------------------------------------
    public function up(): void
    {
        if (! Schema::hasTable('academic_teacher_classes')) {
            Schema::create('academic_teacher_classes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('academic_stage_id')->constrained('academic_stages')->cascadeOnDelete();
                $table->foreignId('academic_class_id')->constrained('academic_classes')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['employee_id', 'academic_class_id'], 'teach_class_unique');
                $table->index(['employee_id', 'academic_stage_id'], 'teach_stage_class_idx');
            });
        }

        if (! Schema::hasTable('academic_teacher_subjects')) {
            Schema::create('academic_teacher_subjects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->foreignId('academic_stage_id')->constrained('academic_stages')->cascadeOnDelete();
                $table->foreignId('academic_subject_id')->constrained('academic_subjects')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['employee_id', 'academic_stage_id', 'academic_subject_id'], 'teach_stage_sub_unique');
                $table->index(['employee_id', 'academic_stage_id'], 'teach_stage_sub_idx');
            });
        }
    }

    //------------------------------------------
    public function down(): void
    {
        Schema::dropIfExists('academic_teacher_subjects');
        Schema::dropIfExists('academic_teacher_classes');
    }
};
