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
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->foreignId('fiscal_year_id')->nullable()->constrained('fiscal_years')->nullOnDelete();
            $table->foreignId('journal_id')->constrained('journals')->cascadeOnDelete();
            $table->string('entry_number', 50);
            $table->date('entry_date');
            $table->date('accounting_date')->nullable();
            $table->string('reference', 100)->nullable();
            $table->string('entry_type', 30)->default('manual');
            $table->string('currency', 10)->default('KWD');
            $table->decimal('exchange_rate', 12, 6)->default(1.000000);
            $table->string('status', 20)->default('draft');
            $table->decimal('total_debit', 15, 4)->default(0.0000);
            $table->decimal('total_credit', 15, 4)->default(0.0000);
            $table->boolean('is_balanced')->default(true);
            $table->text('narration');
            $table->timestamp('posted_at')->nullable();
            $table->foreignId('posted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reversed_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            $table->boolean('is_reversal')->default(false);
            $table->string('hash', 128)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['entity_id', 'entry_number']);
            $table->index(['entity_id', 'entry_date']);
            $table->index(['journal_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
    }
};
