<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //===============================
    public function up(): void
    {
        Schema::create('accounting_payment_matchings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->foreignId('debit_line_id')->constrained('journal_entry_lines')->cascadeOnDelete();
            $table->foreignId('credit_line_id')->constrained('journal_entry_lines')->cascadeOnDelete();
            $table->decimal('matched_amount', 15, 4);
            $table->text('notes')->nullable();
            $table->foreignId('matched_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('matched_at')->useCurrent();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['entity_id', 'debit_line_id']);
            $table->index(['entity_id', 'credit_line_id']);
        });
    }

    //===============================
    public function down(): void
    {
        Schema::dropIfExists('accounting_payment_matchings');
    }
};
