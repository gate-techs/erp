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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code', 50)->unique(); // EMP-0001
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('avatar')->nullable();

            // 1. Personal & Civil Info
            $table->string('gender', 10)->default('male'); // male, female
            $table->string('marital_status', 20)->nullable(); // single, married, divorced, widowed
            $table->string('nationality', 50)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('national_id', 50)->nullable()->unique();
            $table->string('blood_type', 5)->nullable();
            $table->foreignId('visa_type_id')->nullable()->constrained('hr_settings')->nullOnDelete();
            $table->date('visa_expiry_date')->nullable();
            $table->string('passport_number', 50)->nullable();
            $table->date('passport_issue_date')->nullable();
            $table->date('passport_expiry_date')->nullable();

            // 2. Job & Department Assignment
            $table->foreignId('primary_branch_id')->nullable()->constrained('nursery_branches')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('job_title_id')->nullable()->constrained('job_titles')->nullOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('work_schedule_id')->nullable()->constrained('work_schedules')->nullOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->string('employment_type', 20)->default('full_time'); // full_time, part_time, contract, intern
            $table->date('hire_date')->index();
            $table->date('probation_end_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->string('status', 20)->default('active')->index(); // active, on_leave, probation, suspended, resigned, terminated

            // 3. Contact & Location
            $table->string('phone', 30)->index();
            $table->string('alternate_phone', 30)->nullable();
            $table->string('email', 100)->nullable();
            $table->foreignId('governorate_id')->nullable()->constrained('general_settings')->nullOnDelete();
            $table->string('city', 100)->nullable();

            // 4. Emergency Contact
            $table->string('emergency_contact_name', 100)->nullable();
            $table->string('emergency_contact_relation', 50)->nullable();
            $table->string('emergency_contact_phone', 30)->nullable();

            // 5. Bank & Financial Setup
            $table->foreignId('bank_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->foreignId('bank_branch_id')->nullable()->constrained('banks')->nullOnDelete();
            $table->string('account_holder_name', 150)->nullable();
            $table->string('account_number', 50)->nullable();
            $table->string('iban', 50)->nullable();
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->decimal('daily_working_hours', 4, 2)->default(8.0);
            $table->string('social_insurance_number', 50)->nullable();
            $table->decimal('social_insurance_percentage', 5, 2)->default(0);

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
        Schema::dropIfExists('employees');
    }
};
