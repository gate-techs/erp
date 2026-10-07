<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Academic Lessons Table
        Schema::create('academic_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('nurseries')->cascadeOnDelete();
            $table->foreignId('academic_stage_id')->constrained('academic_stages')->cascadeOnDelete();
            $table->foreignId('academic_subject_id')->constrained('academic_subjects')->cascadeOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('image')->nullable();
            $table->integer('order')->default(0);
            $table->string('status', 20)->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['institution_id', 'academic_stage_id', 'academic_subject_id'], 'inst_stage_sub_idx');
            $table->index(['status', 'order']);
        });

        // 2. Academic Lesson Translations Table (Astrotomic)
        Schema::create('academic_lesson_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_lesson_id')->constrained('academic_lessons')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('title');
            $table->longText('content')->nullable();

            $table->unique(['academic_lesson_id', 'locale']);
        });

        // 3. Academic Lesson Attachments Table
        Schema::create('academic_lesson_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_lesson_id')->constrained('academic_lessons')->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('academic_lesson_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_lesson_attachments');
        Schema::dropIfExists('academic_lesson_translations');
        Schema::dropIfExists('academic_lessons');
    }
};
