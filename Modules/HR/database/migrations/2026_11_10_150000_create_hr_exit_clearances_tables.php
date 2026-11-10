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
        // 1. طلبات إخلاء الطرف الرئيسية (Exit Clearance Requests)
        Schema::create('hr_exit_clearances', function (Blueprint $table) {
            $table->id();
            $table->string('clearance_code', 50)->unique(); // e.g. CLR-2026-0001
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('job_title_id')->nullable()->constrained('job_titles')->nullOnDelete();
            
            $table->date('resignation_date')->nullable();
            $table->date('last_working_day');
            $table->string('leaving_reason')->default('resignation'); // resignation, contract_expiry, termination, retirement, other
            $table->text('reason_details')->nullable();
            $table->string('status', 30)->default('pending'); // pending, in_progress, cleared, rejected, cancelled

            // Financial Settlements Summary
            $table->decimal('outstanding_loans_amount', 12, 2)->default(0.00);
            $table->decimal('leave_balance_days', 8, 2)->default(0.00);
            $table->decimal('end_of_service_amount', 12, 2)->default(0.00);
            $table->decimal('net_settlement_amount', 12, 2)->default(0.00);

            // Audit & Final Sign-off
            $table->foreignId('final_approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('final_approved_at')->nullable();
            $table->text('final_notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['employee_id', 'status']);
            $table->index(['status', 'last_working_day']);
        });

        // 2. بنود وموافقات الأقسام (Department Approvals / Checklists)
        Schema::create('hr_exit_clearance_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exit_clearance_id')->constrained('hr_exit_clearances')->cascadeOnDelete();
            $table->string('department_key', 50); // manager, it, finance, hr, facilities, warehouse
            $table->string('department_name'); // اسم القسم بالعربي أو الإنجليزي للعرض
            
            $table->string('status', 30)->default('pending'); // pending, cleared, has_dues, rejected
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            
            $table->text('notes')->nullable(); // ملاحظات تسليم العهد / الأصول
            $table->decimal('dues_amount', 12, 2)->default(0.00); // أي مبالغ أو عهد معلقة مطلوب تحصيلها
            
            $table->json('items_checklist')->nullable(); // قائمة فرعية بالعناصر (مثل: Laptop, ID Card, Email Closed)
            $table->integer('sort_order')->default(1);
            $table->timestamps();

            $table->index(['exit_clearance_id', 'department_key'], 'clr_appr_dept_idx');
            $table->index(['exit_clearance_id', 'status'], 'clr_appr_stat_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hr_exit_clearance_approvals');
        Schema::dropIfExists('hr_exit_clearances');
    }
};

