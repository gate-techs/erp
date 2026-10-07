<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_fee_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institution_id')->nullable()->index();
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->string('code')->nullable()->index();
            $table->string('status', 20)->default('active')->index();
            $table->integer('order')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('academic_fee_type_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_fee_type_id')->constrained('academic_fee_types')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['academic_fee_type_id', 'locale'], 'fee_type_trans_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_fee_type_translations');
        Schema::dropIfExists('academic_fee_types');
    }
};
