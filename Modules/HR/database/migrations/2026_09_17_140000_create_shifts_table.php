<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //======================
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->nullable()->unique();
            $table->time('start_time');
            $table->time('end_time');
            $table->time('checkin_start')->nullable();
            $table->time('checkin_end')->nullable();
            $table->time('checkout_start')->nullable();
            $table->time('checkout_end')->nullable();
            $table->unsignedSmallInteger('grace_period_in')->default(15);
            $table->unsignedSmallInteger('grace_period_out')->default(0);
            $table->decimal('total_hours', 5, 2)->nullable();
            $table->boolean('is_overnight')->default(false);
            $table->string('color', 20)->default('#7367f0');
            $table->string('status', 20)->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    //======================
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
