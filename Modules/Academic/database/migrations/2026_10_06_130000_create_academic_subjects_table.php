<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Academic Subjects Table
        Schema::create('academic_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('nurseries')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->string('image')->nullable();
            $table->string('status', 20)->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['institution_id', 'status']);
            $table->index('academic_year_id');
        });

        // 2. Academic Subject Translations Table (Astrotomic)
        Schema::create('academic_subject_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_subject_id')->constrained('academic_subjects')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unique(['academic_subject_id', 'locale']);
        });

        // 3. Academic Subject Stage Pivot Table (Multi-Select Stages)
        Schema::create('academic_subject_stage', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_subject_id')->constrained('academic_subjects')->cascadeOnDelete();
            $table->foreignId('academic_stage_id')->constrained('academic_stages')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['academic_subject_id', 'academic_stage_id'], 'sub_stage_unique');
        });

        // 4. Academic Subject Attachments Table
        Schema::create('academic_subject_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_subject_id')->constrained('academic_subjects')->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('academic_subject_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_subject_attachments');
        Schema::dropIfExists('academic_subject_stage');
        Schema::dropIfExists('academic_subject_translations');
        Schema::dropIfExists('academic_subjects');
    }
};
