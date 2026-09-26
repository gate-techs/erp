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
        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignId('cost_center_id')->nullable()->constrained('accounting_cost_centers')->nullOnDelete();
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->string('partner_type', 100)->nullable();
            $table->string('partner_name', 255)->nullable();
            $table->string('description', 500)->nullable();
            $table->decimal('debit', 15, 4)->default(0.0000);
            $table->decimal('credit', 15, 4)->default(0.0000);
            $table->string('currency', 10)->default('KWD');
            $table->decimal('amount_currency', 15, 4)->nullable();
            $table->boolean('reconciled')->default(false);
            $table->string('matching_number', 50)->nullable();
            $table->integer('line_order')->default(0);
            $table->timestamps();

            $table->index('journal_entry_id');
            $table->index('account_id');
            $table->index('cost_center_id');
            $table->index(['partner_type', 'partner_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entry_lines');
    }
};
