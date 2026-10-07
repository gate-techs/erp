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
        Schema::dropIfExists('academic_monthly_plan_attachments');
        Schema::dropIfExists('academic_monthly_plan_classes');
        Schema::dropIfExists('academic_monthly_plan_translations');
        Schema::dropIfExists('academic_monthly_plans');

        Schema::create('academic_monthly_plans', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->foreignId('institution_id')->constrained('nurseries', 'id', 'fk_amp_inst')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('nursery_branches', 'id', 'fk_amp_branch')->nullOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years', 'id', 'fk_amp_year')->nullOnDelete();
            $table->foreignId('academic_stage_id')->nullable()->constrained('academic_stages', 'id', 'fk_amp_stage')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status', 20)->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users', 'id', 'fk_amp_user')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('academic_monthly_plan_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_monthly_plan_id')->constrained('academic_monthly_plans', 'id', 'fk_ampt_plan')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('title');
            $table->text('description')->nullable();

            $table->unique(['academic_monthly_plan_id', 'locale'], 'amp_trans_unique');
        });

        Schema::create('academic_monthly_plan_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_monthly_plan_id')->constrained('academic_monthly_plans', 'id', 'fk_ampc_plan')->cascadeOnDelete();
            $table->foreignId('academic_class_id')->constrained('academic_classes', 'id', 'fk_ampc_class')->cascadeOnDelete();

            $table->unique(['academic_monthly_plan_id', 'academic_class_id'], 'amp_class_unique');
        });

        Schema::create('academic_monthly_plan_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_monthly_plan_id')->constrained('academic_monthly_plans', 'id', 'fk_ampa_plan')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type', 100)->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users', 'id', 'fk_ampa_user')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_monthly_plan_attachments');
        Schema::dropIfExists('academic_monthly_plan_classes');
        Schema::dropIfExists('academic_monthly_plan_translations');
        Schema::dropIfExists('academic_monthly_plans');
    }
};
