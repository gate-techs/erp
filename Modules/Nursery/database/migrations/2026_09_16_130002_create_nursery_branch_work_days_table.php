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
        Schema::create('nursery_branch_work_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nursery_branch_id')->constrained('nursery_branches')->cascadeOnDelete();
            $table->string('day_name');
            $table->time('from_time')->nullable();
            $table->time('to_time')->nullable();
            $table->boolean('is_off')->default(false);
            $table->timestamps();

            $table->index(['nursery_branch_id', 'day_name']);
        });
    }

    //======================
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nursery_branch_work_days');
    }
};
