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
        Schema::create('settings_email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('module')->default('general'); // tasks, hr, accounting, crm, general
            $table->string('name');
            $table->string('name_ar');
            $table->text('description')->nullable();
            $table->string('subject_ar');
            $table->string('subject_en');
            $table->longText('body_ar');
            $table->longText('body_en');
            $table->json('variables')->nullable();
            $table->string('action_button_text_ar')->nullable();
            $table->string('action_button_text_en')->nullable();
            $table->string('action_url_variable')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings_email_templates');
    }
};
