<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //===============================
    public function up(): void
    {
        Schema::create('accounting_cost_centers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entity_id')->constrained('entities')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('accounting_cost_centers')->nullOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('nursery_branches')->nullOnDelete();
            $table->string('code', 50);
            $table->string('type', 20)->default('cost')->index();
            $table->string('status', 20)->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['entity_id', 'code']);
            $table->index(['entity_id', 'status']);
            $table->index(['entity_id', 'type']);
            $table->index('parent_id');
        });
    }

    //===============================
    public function down(): void
    {
        Schema::dropIfExists('accounting_cost_centers');
    }
};
