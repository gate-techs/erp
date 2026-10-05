<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create bilingual academic class names and descriptions.
     */
    public function up(): void
    {
        Schema::create('academic_class_translations', function (Blueprint $table) {
            $table->id();

            // Academic class record that owns this translation.
            $table->foreignId('academic_class_id')
                ->constrained('academic_classes')
                ->cascadeOnDelete()
                ->comment('Academic class being translated');

            // Supported application locale, such as ar or en.
            $table->string('locale', 10)
                ->comment('Translation locale');

            // Localized class name.
            $table->string('name', 150)
                ->comment('Localized academic class name');

            // Optional localized class description.
            $table->text('description')
                ->nullable()
                ->comment('Localized academic class description');

            $table->unique(
                ['academic_class_id', 'locale'],
                'academic_class_translations_class_locale_unique'
            );
        });
    }

    /**
     * Drop the academic class translations table.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_class_translations');
    }
};
