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
        Schema::create('hr_permission_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->date('date')->index();
            $table->string('type', 30)->index(); // late_arrival, early_departure, during_shift, emergency
            $table->time('from_time');
            $table->time('to_time');
            $table->decimal('hours_count', 4, 2); // e.g. 1.50 hours
            $table->string('status', 30)->default('pending')->index();
            $table->boolean('is_emergency')->default(false)->index();
            $table->text('reason')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('attachment')->nullable();
            $table->string('financial_policy', 50)->nullable(); // paid, deducted, deduct_from_annual
            
            // Audit & Approvals (with created_by)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('manager_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('manager_approved_at')->nullable();
            $table->foreignId('hr_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hr_approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_permission_requests');
    }
};
