<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('nurseries')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained('nursery_branches')->cascadeOnDelete();
            $table->date('report_date');
            $table->unsignedInteger('children_total')->default(0);
            $table->unsignedInteger('children_present')->default(0);
            $table->unsignedInteger('children_absent')->default(0);
            $table->unsignedInteger('children_withdrawn')->default(0);
            $table->text('children_withdrawal_reason')->nullable();
            $table->unsignedInteger('team_total')->default(0);
            $table->unsignedInteger('team_present')->default(0);
            $table->unsignedInteger('team_absent')->default(0);
            $table->text('team_absence_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['branch_id', 'report_date'], 'daily_report_branch_date_unique');
            $table->index(['institution_id', 'report_date']);
        });

        Schema::create('daily_report_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained('daily_reports')->cascadeOnDelete();
            $table->string('category', 30)->index();
            $table->text('incident')->nullable();
            $table->text('action_taken')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['daily_report_id', 'category', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_report_entries');
        Schema::dropIfExists('daily_reports');
    }
};
