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
        Schema::create('meeting_calendars', function (Blueprint $table) {
            $table->id();

            // Ownership & Context
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->comment('صاحب رزنامة الاجتماعات');

            $table->foreignId('institution_id')
                ->nullable()
                ->constrained('nurseries')
                ->nullOnDelete()
                ->comment('الحضانة / المؤسسة التعليمية (اختياري)');

            // Meeting Details
            $table->string('title')->comment('عنوان الاجتماع');
            $table->text('description')->nullable()->comment('الوصف والملاحظات ومحاور الاجتماع');

            // Date & Time (Meetings have explicit times, no all-day concept)
            $table->date('event_date')->comment('تاريخ الاجتماع');
            $table->time('start_time')->comment('وقت بدء الاجتماع');
            $table->time('end_time')->nullable()->comment('وقت انتهاء الاجتماع');

            // Status & Appearance
            $table->string('status', 20)->default('planned')->index()->comment('الحالة: planned, in_progress, completed, cancelled');
            $table->string('color', 20)->default('#0b3252')->comment('لون الاجتماع في التقويم');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'event_date', 'status'], 'meeting_cal_user_date_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_calendars');
    }
};
