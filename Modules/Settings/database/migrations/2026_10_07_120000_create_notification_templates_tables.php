<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings_notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->string('type', 100)->index();
            $table->string('module', 50)->index();
            $table->string('name_ar');
            $table->string('name_en')->nullable();
            $table->text('description')->nullable();
            $table->string('icon', 100)->nullable();
            $table->string('color', 30)->nullable();
            $table->json('variables')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('settings_notification_template_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')
                ->constrained('settings_notification_templates')
                ->cascadeOnDelete();
            $table->string('locale', 10);
            $table->string('title');
            $table->text('message');
            $table->string('action_url')->nullable();
            $table->timestamps();
            $table->unique(['template_id', 'locale'], 'settings_ntt_template_locale_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings_notification_template_translations');
        Schema::dropIfExists('settings_notification_templates');
    }
};
