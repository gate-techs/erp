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
        Schema::create('settings_print_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_ar')->nullable();
            $table->string('code')->unique();
            $table->string('type')->default('report'); // report, financial, letter
            $table->text('description')->nullable();
            $table->string('page_size', 20)->default('A4'); // A4, A5, Letter
            $table->string('orientation', 20)->default('portrait'); // portrait, landscape
            $table->json('margins')->nullable();
            $table->json('header_settings')->nullable();
            $table->json('footer_settings')->nullable();
            $table->json('signature_settings')->nullable();
            $table->longText('custom_css')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings_print_templates');
    }
};
