<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //===============================
    public function up(): void
    {
        Schema::create('bank_reconciliation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_reconciliation_id')->constrained('bank_reconciliations')->cascadeOnDelete();
            $table->foreignId('journal_entry_line_id')->nullable()->constrained('journal_entry_lines')->nullOnDelete();
            $table->string('type', 30)->default('matched_entry')->index();
            $table->string('statement_reference', 100)->nullable();
            $table->date('transaction_date');
            $table->decimal('debit', 15, 4)->default(0);
            $table->decimal('credit', 15, 4)->default(0);
            $table->boolean('is_cleared')->default(true)->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['bank_reconciliation_id', 'journal_entry_line_id'], 'rec_items_rec_line_idx');
        });
    }

    //===============================
    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliation_items');
    }
};
