<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //--------------------------------------------------
    public function up(): void
    {
        Schema::create('entity_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->string('locale', 5)->index();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unique(['entity_id', 'locale']);
        });
    }

    //--------------------------------------------------
    public function down(): void
    {
        Schema::dropIfExists('entity_translations');
    }
};
