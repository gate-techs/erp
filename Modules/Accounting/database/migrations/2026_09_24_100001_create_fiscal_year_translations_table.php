<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fiscal_year_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_year_id')->constrained('fiscal_years')->cascadeOnDelete();
            $table->string('locale', 5)->index();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unique(['fiscal_year_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fiscal_year_translations');
    }
};
