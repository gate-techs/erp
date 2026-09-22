<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //--------------------------------------------------
    public function up(): void
    {
        Schema::create('account_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->string('locale', 5)->index();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unique(['account_id', 'locale']);
        });
    }

    //--------------------------------------------------
    public function down(): void
    {
        Schema::dropIfExists('account_translations');
    }
};
