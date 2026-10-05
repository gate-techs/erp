<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create bilingual academic stage names and descriptions.
     */
    public function up(): void
    {
        Schema::create('academic_stage_translations', function (Blueprint $table) {
            $table->id();

            // Academic stage record that owns this translation.
            $table->foreignId('academic_stage_id')
                ->constrained('academic_stages')
                ->cascadeOnDelete()
                ->comment('Academic stage being translated');

            // Supported application locale, such as ar or en.
            $table->string('locale', 10)
                ->comment('Translation locale');

            // Localized stage name.
            $table->string('name', 150)
                ->comment('Localized academic stage name');

            // Optional localized stage description.
            $table->text('description')
                ->nullable()
                ->comment('Localized academic stage description');

            $table->unique(
                ['academic_stage_id', 'locale'],
                'academic_stage_translations_stage_locale_unique'
            );
        });
    }

    /**
     * Drop the academic stage translations table.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_stage_translations');
    }
};
