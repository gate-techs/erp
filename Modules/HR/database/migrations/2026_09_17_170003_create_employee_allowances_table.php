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
        Schema::create('employee_allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('allowance_type_id')->constrained('hr_settings')->cascadeOnDelete();
            $table->decimal('amount', 10, 2)->default(0);
            $table->boolean('is_percentage')->default(false);
            $table->boolean('is_recurring')->default(true);
            $table->date('effective_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    //======================
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_allowances');
    }
};
