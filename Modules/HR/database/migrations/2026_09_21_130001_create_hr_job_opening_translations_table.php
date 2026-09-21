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
        Schema::create('hr_job_opening_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_opening_id')->constrained('hr_job_openings')->cascadeOnDelete();
            $table->string('locale', 5)->index();
            $table->string('title');
            $table->string('education_level')->nullable();
            $table->text('job_summary')->nullable();
            $table->longText('responsibilities')->nullable();
            $table->longText('requirements')->nullable();
            $table->longText('benefits')->nullable();

            $table->unique(['job_opening_id', 'locale']);
        });

        // Make title on parent table nullable since title is stored in translation table
        Schema::table('hr_job_openings', function (Blueprint $table) {
            $table->string('title')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_job_opening_translations');
    }
};
