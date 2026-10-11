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
        Schema::create('events', function (Blueprint $table) {
            $table->id();

            // Institutional context
            $table->foreignId('institution_id')
                ->constrained('nurseries')
                ->cascadeOnDelete()
                ->comment('المؤسسة / الحضانة التابع لها الحدث');

            // Event Timing (التاريخ - إلى تاريخ)
            $table->date('start_date')->comment('التاريخ (تاريخ بداية الحدث)');
            $table->date('end_date')->nullable()->comment('إلى تاريخ (تاريخ نهاية الحدث)');

            // Status
            $table->string('status', 20)->default('active')->index()->comment('حالة الحدث: active, inactive');

            // User attribution
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('المستخدم الذي أنشأ الحدث');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['institution_id', 'start_date', 'end_date'], 'events_inst_dates_idx');
        });

        Schema::create('event_translations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('event_id')
                ->constrained('events')
                ->cascadeOnDelete()
                ->comment('معرّف الحدث');

            $table->string('locale', 10)->index()->comment('كود اللغة (ar / en)');
            $table->string('title')->comment('اسم الحدث');
            $table->text('description')->nullable()->comment('وصف الحدث');

            $table->unique(['event_id', 'locale'], 'event_trans_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_translations');
        Schema::dropIfExists('events');
    }
};
