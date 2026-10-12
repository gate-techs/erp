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
        Schema::create('personal_calendars', function (Blueprint $table) {
            $table->id();

            // Ownership & Context
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('صاحب الرزنامة الشخصية');

            $table->foreignId('institution_id')
                ->nullable()
                ->constrained('nurseries')
                ->nullOnDelete()
                ->comment('الحضانة / المؤسسة التعليمية (اختياري)');

            // Event Details
            $table->string('title')->comment('عنوان الموعد / المهمة');
            $table->text('description')->nullable()->comment('الوصف والملاحظات');

            // Date & Time
            $table->date('event_date')->comment('تاريخ الموعد');
            $table->time('start_time')->nullable()->comment('من ساعة');
            $table->time('end_time')->nullable()->comment('إلى ساعة');
            $table->boolean('is_all_day')->default(false)->comment('طوال اليوم');

            // Status & Appearance
            $table->string('status', 20)->default('planned')->index()->comment('الحالة: planned, in_progress, completed, cancelled');
            $table->string('color', 20)->default('#0b3252')->comment('لون الموعد في التقويم');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'event_date', 'status'], 'personal_cal_user_date_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_calendars');
    }
};
