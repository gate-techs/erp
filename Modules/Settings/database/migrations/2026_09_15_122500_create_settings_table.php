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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // Contact Information
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('emergency_phone')->nullable();

            // Branding & Media
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();

            // Regional & Financial Settings
            $table->string('currency')->default('KWD');
            $table->string('currency_symbol')->default('د.ك');
            $table->string('timezone')->default('Asia/Kuwait');
            $table->string('default_locale')->default('ar');
            $table->string('tax_number')->nullable();

            // Social Media Links
            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('youtube')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
