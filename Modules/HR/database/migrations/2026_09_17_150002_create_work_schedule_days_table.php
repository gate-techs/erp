<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //======================
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('work_schedule_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_schedule_id')->constrained('work_schedules')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 0=Sunday, 1=Monday, ..., 6=Saturday (matches Carbon)
            $table->boolean('is_off_day')->default(false);
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->foreignId('second_shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->decimal('work_hours', 4, 2)->default(0);
            $table->timestamps();

            $table->unique(['work_schedule_id', 'day_of_week'], 'ws_days_ws_id_day_unique');
        });
    }

    //======================
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedule_days');
    }
};
