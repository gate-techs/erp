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
        // 1. Tasks Main Table
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_group_id')->nullable()->constrained('task_groups')->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('priority')->default('medium');       // low, medium, high, urgent
            $table->string('task_status')->default('pending');    // pending, in_progress, on_hold, completed, cancelled
            $table->date('start_date')->nullable();
            $table->date('completion_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Task Translations Table
        Schema::create('task_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('title');
            $table->longText('body')->nullable();

            $table->unique(['task_id', 'locale']);
        });

        // 3. Task Attachments Table
        Schema::create('task_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_attachments');
        Schema::dropIfExists('task_translations');
        Schema::dropIfExists('tasks');
    }
};
