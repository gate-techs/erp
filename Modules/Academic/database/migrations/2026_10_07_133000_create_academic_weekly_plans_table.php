<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('academic_weekly_plan_attachments');
        Schema::dropIfExists('academic_weekly_plan_translations');
        Schema::dropIfExists('academic_weekly_plans');

        // 1. Academic Weekly Plans Table
        Schema::create('academic_weekly_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('nurseries')->cascadeOnDelete();
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->foreignId('academic_stage_id')->constrained('academic_stages')->cascadeOnDelete();
            $table->foreignId('academic_class_id')->constrained('academic_classes')->cascadeOnDelete();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 20)->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['institution_id', 'branch_id', 'academic_stage_id', 'academic_class_id'], 'awp_scope_idx');
            $table->index(['status', 'start_date', 'end_date'], 'awp_status_dates_idx');
        });

        // 2. Academic Weekly Plan Translations Table (Astrotomic)
        Schema::create('academic_weekly_plan_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_weekly_plan_id')
                ->constrained('academic_weekly_plans', 'id', 'awp_trans_plan_fk')
                ->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('title');
            $table->longText('content')->nullable();

            $table->unique(['academic_weekly_plan_id', 'locale'], 'awp_trans_unique');
        });

        // 3. Academic Weekly Plan Attachments Table
        Schema::create('academic_weekly_plan_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_weekly_plan_id')
                ->constrained('academic_weekly_plans', 'id', 'awp_attach_plan_fk')
                ->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('academic_weekly_plan_id', 'awp_attach_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_weekly_plan_attachments');
        Schema::dropIfExists('academic_weekly_plan_translations');
        Schema::dropIfExists('academic_weekly_plans');
    }
};
