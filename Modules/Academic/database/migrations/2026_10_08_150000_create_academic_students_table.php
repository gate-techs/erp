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
        Schema::create('academic_students', function (Blueprint $table) {
            $table->id();
            $table->string('student_code', 50)->nullable()->unique();
            $table->string('civil_id', 30)->nullable()->index();

            // Educational & Hierarchy Links
            $table->foreignId('institution_id')->constrained('nurseries')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('nursery_branches')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->foreignId('academic_stage_id')->nullable()->constrained('academic_stages')->nullOnDelete();
            $table->foreignId('academic_class_id')->nullable()->constrained('academic_classes')->nullOnDelete();
            $table->foreignId('nationality_id')->nullable()->constrained('general_settings')->nullOnDelete();
            $table->foreignId('academic_guardian_id')->nullable()->constrained('academic_guardians')->nullOnDelete();

            // Personal Info
            $table->date('birth_date')->nullable();
            $table->tinyInteger('gender')->default(1)->index(); // 1 = Male (ذكر), 2 = Female (أنثى)
            $table->string('photo')->nullable();
            $table->string('status', 20)->default('active')->index(); // active, pending, inactive, graduated, withdrawn

            // Terms & Declarations / Consents
            $table->boolean('terms_accepted')->default(false);
            $table->timestamp('terms_accepted_at')->nullable();
            $table->boolean('financial_policy_accepted')->default(false);
            $table->timestamp('financial_policy_accepted_at')->nullable();
            $table->boolean('pickup_policy_accepted')->default(false);
            $table->timestamp('pickup_policy_accepted_at')->nullable();
            $table->boolean('photo_consent')->default(true);
            $table->text('photo_consent_notes')->nullable();

            // Notes
            $table->text('guardian_notes')->nullable();
            $table->text('admin_notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('academic_student_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_student_id')->constrained('academic_students')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->timestamps();

            $table->unique(['academic_student_id', 'locale'], 'student_locale_unique');
        });
    }

    //------------------------------------------
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_student_translations');
        Schema::dropIfExists('academic_students');
    }
};
