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
        // Father & Mother Details for Student
        Schema::create('academic_student_parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_student_id')->constrained('academic_students')->cascadeOnDelete();
            $table->tinyInteger('parent_type')->index(); // 1 = Father (أب), 2 = Mother (أم)
            $table->foreignId('academic_guardian_id')->nullable()->constrained('academic_guardians')->nullOnDelete();

            $table->string('name');
            $table->string('civil_id', 30)->nullable()->index();
            $table->string('nationality', 100)->nullable();
            $table->string('occupation', 100)->nullable();
            $table->string('workplace', 150)->nullable();
            $table->string('company_branch', 150)->nullable();
            $table->tinyInteger('marital_status')->nullable(); // 1 = Single, 2 = Married, 3 = Divorced, 4 = Widowed
            $table->string('phone', 30)->nullable();
            $table->string('email', 100)->nullable();

            $table->timestamps();
        });

        // Siblings in nursery
        Schema::create('academic_student_siblings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_student_id')->constrained('academic_students')->cascadeOnDelete();
            $table->foreignId('sibling_student_id')->nullable()->constrained('academic_students')->nullOnDelete();

            $table->string('sibling_name');
            $table->foreignId('academic_class_id')->nullable()->constrained('academic_classes')->nullOnDelete();
            $table->string('class_name', 100)->nullable();

            $table->timestamps();
        });
    }

    //------------------------------------------
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_student_siblings');
        Schema::dropIfExists('academic_student_parents');
    }
};
