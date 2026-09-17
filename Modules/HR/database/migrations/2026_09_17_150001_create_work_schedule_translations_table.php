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
        Schema::create('work_schedule_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_schedule_id')->constrained('work_schedules')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unique(['work_schedule_id', 'locale'], 'ws_trans_ws_id_locale_unique');
        });
    }

    //======================
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedule_translations');
    }
};
