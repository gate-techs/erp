<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('institution_id')->index();
            $table->unsignedBigInteger('branch_id')->nullable()->index();
            $table->unsignedBigInteger('academic_year_id')->index();
            $table->unsignedBigInteger('academic_stage_id')->index();
            $table->foreignId('academic_fee_type_id')->constrained('academic_fee_types')->cascadeOnDelete();
            $table->string('billing_period', 50)->default('monthly')->index(); // one_time, monthly, term, annually
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->string('image')->nullable();
            $table->string('status', 20)->default('active')->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('academic_fee_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_fee_id')->constrained('academic_fees')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['academic_fee_id', 'locale'], 'fee_trans_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_fee_translations');
        Schema::dropIfExists('academic_fees');
    }
};
