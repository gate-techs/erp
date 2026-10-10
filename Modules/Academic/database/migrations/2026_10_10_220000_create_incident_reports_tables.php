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
        if (! Schema::hasTable('incident_reports')) {
            Schema::create('incident_reports', function (Blueprint $table) {
                $table->id();

                // 1. الطالب المعني بالحادث
                $table->foreignId('student_id')->constrained('academic_students')->cascadeOnDelete();

                // 2. نوع الحادث / الإصابة (مرتبط بجدول الإعدادات general_settings)
                $table->foreignId('incident_type_id')->nullable()->constrained('general_settings')->nullOnDelete();

                // 3. المسؤول / الموظف أو الممرضة المسجلة
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

                // 4. التاريخ والوقت
                $table->date('date')->index();
                $table->string('time', 20)->nullable();

                // 5. المكان (مثل: الملعب، الفصل، الحمام، إلخ)
                $table->string('location')->nullable();

                // 6. وصف الطالب / الواقعة
                $table->longText('student_description')->nullable();

                // 7. الإجراءات المتخذة والإسعافات الأولية
                $table->longText('procedures')->nullable();

                // 8. الحاضرون / الشهود (مصفوفة JSON بمعرفات الموظفين الحاضرين)
                $table->json('persons')->nullable();

                // 9. تقرير الممرضة والحالة الصحية
                $table->longText('nurse_report')->nullable();

                // المؤسسة والفرع لتسهيل التصفية والصلاحيات
                $table->foreignId('institution_id')->nullable()->constrained('nurseries')->nullOnDelete();
                $table->foreignId('branch_id')->nullable()->constrained('nursery_branches')->nullOnDelete();

                // تتبع العمليات
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('deleted_by')->nullable()->constrained('users')->nullOnDelete();

                $table->timestamps();
                $table->softDeletes();

                $table->index(['student_id', 'date']);
                $table->index('incident_type_id');
                $table->index('user_id');
                $table->index(['institution_id', 'branch_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_reports');
    }
};
