<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_task_providers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained('ai_tasks')->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained('ai_providers')->cascadeOnDelete();
            $table->unsignedSmallInteger('priority')->default(1);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['task_id', 'provider_id']);
            $table->index(['task_id', 'is_default', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_task_providers');
    }
};
