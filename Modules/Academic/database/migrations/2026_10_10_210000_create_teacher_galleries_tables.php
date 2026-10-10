<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //------------------------------------------
    public function up(): void
    {
        // 1. Main Galleries Table (ألبوم المعرض)
        if (! Schema::hasTable('teacher_galleries')) {
            Schema::create('teacher_galleries', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_id')->nullable()->constrained('employees')->nullOnDelete();
                $table->foreignId('class_room_id')->constrained('academic_classes')->cascadeOnDelete();
                $table->foreignId('student_id')->nullable()->constrained('academic_students')->nullOnDelete();
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->date('date')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['class_room_id', 'date']);
                $table->index('teacher_id');
                $table->index('student_id');
            });
        }

        // 2. Pivot Table for Multiple Selected Students (الطلاب المختارون)
        if (! Schema::hasTable('teacher_gallery_students')) {
            Schema::create('teacher_gallery_students', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_gallery_id')->constrained('teacher_galleries')->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('academic_students')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['teacher_gallery_id', 'student_id'], 't_gal_student_unique');
            });
        }

        // 3. Gallery Images/Media Table (جدول الصور والفيديوهات)
        if (! Schema::hasTable('teacher_gallery_images')) {
            Schema::create('teacher_gallery_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_gallery_id')->constrained('teacher_galleries')->cascadeOnDelete();
                $table->string('file_path');
                $table->string('file_name')->nullable();
                $table->string('file_type', 50)->default('image'); // image, video
                $table->unsignedBigInteger('file_size')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index('teacher_gallery_id');
            });
        }
    }

    //------------------------------------------
    public function down(): void
    {
        Schema::dropIfExists('teacher_gallery_images');
        Schema::dropIfExists('teacher_gallery_students');
        Schema::dropIfExists('teacher_galleries');
    }
};
