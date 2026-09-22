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
        Schema::create('audit_log_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_external_db_enabled')->default(false);
            $table->string('driver')->default('mysql');
            $table->string('host')->nullable();
            $table->string('port')->default('3306')->nullable();
            $table->string('database')->nullable();
            $table->string('username')->nullable();
            $table->text('password')->nullable(); // Stored encrypted
            $table->boolean('fallback_to_local')->default(true);
            $table->boolean('is_queue_enabled')->default(false);
            $table->timestamp('last_tested_at')->nullable();
            $table->string('last_test_status')->nullable();
            $table->text('last_test_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_log_settings');
    }
};
