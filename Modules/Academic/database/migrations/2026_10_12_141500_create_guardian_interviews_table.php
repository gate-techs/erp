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
        Schema::create('guardian_interviews', function (Blueprint $table) {
            $table->id();

            // Ownership & Link
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('الموظف المسؤول / صاحب الرزنامة');

            $table->foreignId('academic_guardian_id')
                ->nullable()
                ->constrained('academic_guardians')
                ->nullOnDelete()
                ->comment('ولي الأمر المسجل');

            $table->foreignId('institution_id')
                ->nullable()
                ->constrained('nurseries')
                ->nullOnDelete()
                ->comment('الحضانة / المؤسسة التعليمية');

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('nursery_branches')
                ->nullOnDelete()
                ->comment('الفرع');

            // Guardian Details
            $table->string('guardian_name')->comment('اسم ولي الأمر');
            $table->string('guardian_civil_id')->nullable()->index()->comment('الرقم المدني لولي الأمر');
            $table->string('guardian_phone')->nullable()->comment('رقم الهاتف');
            $table->string('guardian_email')->nullable()->comment('البريد الإلكتروني');

            // Interview Date & Time
            $table->date('interview_date')->comment('تاريخ المقابلة');
            $table->time('start_time')->comment('وقت المقابلة');
            $table->time('end_time')->nullable()->comment('وقت انتهاء المقابلة');

            // Details & Status
            $table->text('notes')->nullable()->comment('ملاحظات المقابلة');
            $table->string('status', 20)->default('planned')->index()->comment('الحالة: planned, completed, cancelled, absent');
            $table->string('color', 20)->default('#0b3252')->comment('لون الموعد في التقويم');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'interview_date', 'status'], 'guardian_interview_user_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardian_interviews');
    }
};
