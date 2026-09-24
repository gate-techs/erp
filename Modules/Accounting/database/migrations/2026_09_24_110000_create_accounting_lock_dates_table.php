<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_lock_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->date('lock_all_date')->nullable();
            $table->date('tax_lock_date')->nullable();
            $table->date('fiscal_year_lock_date')->nullable();
            $table->date('hard_lock_date')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique('entity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_lock_dates');
    }
};
