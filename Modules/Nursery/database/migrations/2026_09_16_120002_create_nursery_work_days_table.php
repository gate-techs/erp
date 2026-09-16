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
        Schema::create('nursery_work_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nursery_id')->constrained('nurseries')->cascadeOnDelete();
            $table->string('day', 20)->index();
            $table->time('from_time')->nullable();
            $table->time('to_time')->nullable();
            $table->boolean('is_off')->default(false);
            $table->timestamps();

            $table->unique(['nursery_id', 'day']);
        });
    }

    //======================
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nursery_work_days');
    }
};
