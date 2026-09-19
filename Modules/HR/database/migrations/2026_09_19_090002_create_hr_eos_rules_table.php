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
        Schema::create('hr_eos_rules', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50)->default('resignation')->index(); // resignation, termination, contract_end
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->decimal('from_years', 5, 2)->default(0.00);
            $table->decimal('to_years', 5, 2)->nullable(); // null means open / no upper bound
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->integer('sort_order')->default(0);
            $table->string('status', 20)->default('active');
            $table->timestamps();
        });
    }

    //======================
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_eos_rules');
    }
};
