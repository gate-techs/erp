<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medication_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('student_id')->constrained('academic_students')->cascadeOnDelete();
            $table->string('medicine_name');
            $table->string('dosage_amount');
            $table->string('dosage_time');
            $table->date('date');
            $table->foreignId('nurse_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('nurse_note')->nullable();
            $table->timestamp('action_time')->nullable();
            $table->boolean('done')->default(false)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'date']);
            $table->index(['parent_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_requests');
    }
};
