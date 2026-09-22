<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //--------------------------------------------------
    public function up(): void
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->string('code', 50);
            $table->string('account_type', 50)->index();
            $table->string('normal_balance', 10);
            $table->unsignedInteger('level')->default(0);
            $table->boolean('is_group')->default(false);
            $table->boolean('allow_posting')->default(true);
            $table->boolean('reconcile')->default(false);
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_deprecated')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['entity_id', 'code']);
            $table->index(['entity_id', 'parent_id']);
            $table->index(['entity_id', 'level']);
        });
    }

    //--------------------------------------------------
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
