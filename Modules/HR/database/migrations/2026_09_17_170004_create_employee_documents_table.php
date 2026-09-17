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
        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('document_type_id')->nullable()->constrained('hr_settings')->nullOnDelete();
            $table->string('document_number', 100)->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable()->index();
            $table->unsignedSmallInteger('notify_before_days')->default(30);
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
        Schema::dropIfExists('employee_documents');
    }
};
