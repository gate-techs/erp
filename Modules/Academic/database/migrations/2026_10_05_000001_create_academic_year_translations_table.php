<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create bilingual academic year names.
     */
    public function up(): void
    {
        Schema::create('academic_year_translations', function (Blueprint $table) {
            $table->id();

            // Academic year record that owns this translation.
            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->cascadeOnDelete()
                ->comment('Academic year being translated');

            // Supported application locale, such as ar or en.
            $table->string('locale', 10)
                ->comment('Translation locale');

            // Localized display name.
            $table->string('name', 100)
                ->comment('Localized academic year name');

            $table->unique(
                ['academic_year_id', 'locale'],
                'academic_year_translations_year_locale_unique'
            );
        });
    }

    /**
     * Drop the academic year translations table.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_year_translations');
    }
};
