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
        // Information Form Responses (Tabs 8 & 9: InformationForm enum 1..22)
        Schema::create('academic_student_information_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('academic_student_id');
            $table->foreign('academic_student_id', 'stud_info_resp_stud_fk')->references('id')->on('academic_students')->cascadeOnDelete();
            $table->tinyInteger('question_code')->index(); // InformationForm: 1..12 (Form 1), 13..22 (Form 2)
            $table->boolean('answer')->default(false);
            $table->text('details')->nullable();
            $table->timestamps();

            $table->unique(['academic_student_id', 'question_code'], 'student_info_q_unique');
        });
    }

    //------------------------------------------
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_student_information_responses');
    }
};
