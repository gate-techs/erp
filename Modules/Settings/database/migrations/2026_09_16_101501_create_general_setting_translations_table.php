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
        Schema::create('general_setting_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('general_setting_id')->constrained('general_settings')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name');

            $table->unique(['general_setting_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_setting_translations');
    }
};
