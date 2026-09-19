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
        Schema::create('biometric_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('nursery_branches')->nullOnDelete();
            $table->string('name', 100);
            $table->string('ip_address', 50)->nullable();
            $table->integer('port')->default(4370);
            $table->string('device_serial_number', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('protocol', 50)->default('zkteco'); // zkteco, push_sdk, hikvision, dahua
            $table->string('status', 20)->default('active')->index(); // active, inactive, offline
            $table->timestamp('last_sync_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometric_devices');
    }
};
