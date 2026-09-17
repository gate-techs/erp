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
        Schema::create('official_holiday_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('official_holiday_id')->constrained('official_holidays')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unique(['official_holiday_id', 'locale'], 'oh_trans_oh_id_locale_unique');
        });
    }

    //======================
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_holiday_translations');
    }
};
