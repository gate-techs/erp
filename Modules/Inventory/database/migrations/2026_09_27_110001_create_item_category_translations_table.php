<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_category_id')->constrained('item_categories')->cascadeOnDelete();
            $table->string('locale', 5)->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unique(['item_category_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_category_translations');
    }
};
