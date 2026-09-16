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
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('banks')->nullOnDelete(); // null = بنك رئيسي، قيمة = فرع للبنك
            $table->string('code', 50)->nullable()->index(); // كود البنك أو كود الفرع
            $table->string('swift_code', 50)->nullable()->index(); // رمز الـ SWIFT
            $table->string('status', 20)->default('active')->index(); // الحالة: active / inactive
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
        Schema::dropIfExists('banks');
    }
};
