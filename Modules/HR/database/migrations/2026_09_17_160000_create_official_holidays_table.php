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
        Schema::create('official_holidays', function (Blueprint $table) {
            $table->id();
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->unsignedSmallInteger('days_count')->default(1);
            $table->unsignedSmallInteger('year')->index();
            $table->boolean('is_recurring')->default(false)->index();
            $table->foreignId('branch_id')->nullable()->constrained('nursery_branches')->nullOnDelete();
            $table->string('color', 20)->default('#e83e8c');
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
        Schema::dropIfExists('official_holidays');
    }
};
