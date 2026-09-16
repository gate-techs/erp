<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //======================
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nursery_branch_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nursery_branch_id')->constrained('nursery_branches')->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address')->nullable();

            $table->unique(['nursery_branch_id', 'locale']);
        });
    }

    //======================
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nursery_branch_translations');
    }
};
