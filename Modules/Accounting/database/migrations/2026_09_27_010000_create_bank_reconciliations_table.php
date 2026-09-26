<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //===============================
    public function up(): void
    {
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->foreignId('cash_account_id')->constrained('cash_accounts')->cascadeOnDelete();
            $table->string('reconciliation_number', 50);
            $table->date('statement_date');
            $table->decimal('statement_balance', 15, 4)->default(0);
            $table->decimal('book_balance', 15, 4)->default(0);
            $table->decimal('reconciled_balance', 15, 4)->default(0);
            $table->decimal('difference', 15, 4)->default(0);
            $table->string('status', 20)->default('draft')->index();
            $table->text('notes')->nullable();
            $table->timestamp('reconciled_at')->nullable();
            $table->foreignId('reconciled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['entity_id', 'reconciliation_number']);
            $table->index(['entity_id', 'cash_account_id', 'statement_date'], 'bank_rec_entity_acc_date_idx');
        });
    }

    //===============================
    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliations');
    }
};
