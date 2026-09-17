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
        Schema::create('employee_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name'); // الاسم الكامل
            $table->string('address_1')->nullable(); // العنوان الأول
            $table->string('address_2')->nullable(); // العنوان الثاني
            $table->text('notes')->nullable(); // ملاحظات إدارية

            $table->unique(['employee_id', 'locale'], 'emp_trans_emp_id_locale_unique');
        });
    }

    //======================
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_translations');
    }
};
