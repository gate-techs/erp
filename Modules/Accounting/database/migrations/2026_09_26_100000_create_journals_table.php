<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //--------------------------------------------------
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->string('code', 5);
            $table->string('type', 30)->index();
            $table->string('currency', 10)->nullable();

            // Odoo-like numbering configuration.
            $table->string('sequence_prefix', 50)->nullable();
            $table->unsignedBigInteger('sequence_number_next')->default(1);
            $table->boolean('refund_sequence')->default(false);
            $table->boolean('payment_sequence')->default(false);

            // Odoo-like default and control accounts.
            $table->foreignId('default_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('suspense_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('profit_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('loss_account_id')->nullable()->constrained('accounts')->nullOnDelete();

            $table->boolean('allow_manual_entries')->default(true);
            $table->boolean('reconcile')->default(false);
            $table->boolean('restrict_mode_hash_table')->default(false);
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['entity_id', 'code']);
            $table->index(['entity_id', 'type']);
        });
    }

    //--------------------------------------------------
    public function down(): void
    {
        Schema::dropIfExists('journals');
    }
};
