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
        Schema::create('hr_work_policies', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->index(); // leaves, absence, notice_and_eos, permissions
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('type', 30)->default('string'); // number, boolean, string, select
            $table->string('label_ar')->nullable();
            $table->string('label_en')->nullable();
            $table->json('options')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    //======================
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_work_policies');
    }
};
