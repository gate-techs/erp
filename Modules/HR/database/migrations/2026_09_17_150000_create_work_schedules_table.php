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
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable()->unique();
            $table->string('type')->default('fixed')->index(); // fixed, flexible, rotational
            $table->foreignId('branch_id')->nullable()->constrained('nursery_branches')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->unsignedTinyInteger('working_days_count')->default(5);
            $table->decimal('total_weekly_hours', 5, 2)->default(0);
            $table->boolean('is_default')->default(false)->index();
            $table->string('color', 20)->default('#7367f0');
            $table->string('status')->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    //======================
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};
