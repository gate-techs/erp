<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //------------------------------------------
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Chronic Diseases (Tab 3 - DiseaseType enum 1..8)
        Schema::create('academic_student_diseases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_student_id')->constrained('academic_students')->cascadeOnDelete();
            $table->tinyInteger('disease_code')->index(); // DiseaseType: 1=DIABETES, 2=EPILEPSY, ... 8=OTHER
            $table->boolean('has_disease')->default(false);
            $table->string('medication_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['academic_student_id', 'disease_code'], 'student_disease_unique');
        });

        // Medical Questionnaire 1 (Tab 4 - MedicalQuestionnaire1 enum 1..10)
        Schema::create('academic_student_medical_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_student_id')->constrained('academic_students')->cascadeOnDelete();
            $table->tinyInteger('question_code')->index(); // MedicalQuestionnaire1: 1..10
            $table->boolean('answer')->default(false);
            $table->text('details')->nullable();
            $table->timestamps();

            $table->unique(['academic_student_id', 'question_code'], 'student_medical_q_unique');
        });
    }

    //------------------------------------------
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_student_medical_responses');
        Schema::dropIfExists('academic_student_diseases');
    }
};
