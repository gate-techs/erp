<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounting_lock_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('lock_type', 30);
            $table->dateTime('valid_from');
            $table->dateTime('valid_until');
            $table->text('reason');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('revoked_at')->nullable();
            $table->foreignId('revoked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['entity_id', 'lock_type']);
            $table->index(['user_id', 'valid_from', 'valid_until']);
            $table->index('revoked_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounting_lock_exceptions');
    }
};
