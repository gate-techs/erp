<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //===============================
    public function up(): void
    {
        Schema::create('accounting_cost_center_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_center_id')->constrained('accounting_cost_centers')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name', 255);
            $table->text('description')->nullable();

            $table->unique(['cost_center_id', 'locale']);
        });
    }

    //===============================
    public function down(): void
    {
        Schema::dropIfExists('accounting_cost_center_translations');
    }
};
