<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //--------------------------------------------------
    public function up(): void
    {
        Schema::create('journal_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained('journals')->cascadeOnDelete();
            $table->string('locale', 5)->index();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unique(['journal_id', 'locale']);
        });
    }

    //--------------------------------------------------
    public function down(): void
    {
        Schema::dropIfExists('journal_translations');
    }
};
