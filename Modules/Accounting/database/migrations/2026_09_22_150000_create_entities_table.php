<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //--------------------------------------------------
    public function up(): void
    {
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nursery_id')->nullable()->unique()->constrained('nurseries')->nullOnDelete();
            $table->string('code', 50)->unique();
            $table->string('type', 50)->default('nursery')->index();
            $table->string('currency', 10)->default('KWD');
            $table->string('status', 20)->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    //--------------------------------------------------
    public function down(): void
    {
        Schema::dropIfExists('entities');
    }
};
