<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('academic_guardians', function (Blueprint $table) {
            $table->id();
            $table->string('guardian_code', 50)->nullable()->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('institution_id')->constrained('nurseries')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('nursery_branches')->nullOnDelete();
            $table->string('phone_code', 10)->default('+966');
            $table->string('phone', 30)->index();
            $table->string('email', 100)->nullable();
            $table->string('photo')->nullable();
            $table->string('status', 20)->default('active')->index(); // active, inactive
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('academic_guardian_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_guardian_id')->constrained('academic_guardians')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->timestamps();

            $table->unique(['academic_guardian_id', 'locale'], 'guardian_locale_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_guardian_translations');
        Schema::dropIfExists('academic_guardians');
    }
};
