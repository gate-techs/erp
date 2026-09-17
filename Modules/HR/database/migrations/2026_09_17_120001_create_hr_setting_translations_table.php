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
        Schema::create('hr_setting_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hr_setting_id')->constrained('hr_settings')->cascadeOnDelete();
            $table->string('locale', 5)->index();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unique(['hr_setting_id', 'locale']);
        });
    }

    //======================
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_setting_translations');
    }
};
