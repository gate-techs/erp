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
            
            $table->unsignedBigInteger('institution_id')->nullable();
            $table->foreign('institution_id', 'academic_events_institution_fk')
                ->references('id')->on('nurseries')->nullOnDelete();

            $table->unsignedBigInteger('branch_id')->nullable();
            $table->foreign('branch_id', 'academic_events_branch_fk')
                ->references('id')->on('nursery_branches')->nullOnDelete();

            $table->unsignedBigInteger('academic_class_id')->nullable();
            $table->foreign('academic_class_id', 'academic_events_class_fk')
                ->references('id')->on('academic_classes')->nullOnDelete();

            $table->unsignedBigInteger('event_type_id')->nullable();
            $table->foreign('event_type_id', 'academic_events_type_fk')
                ->references('id')->on('general_settings')->nullOnDelete();

            $table->string('image')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('price', 10, 3)->default(0);
            $table->string('status', 20)->default('active');
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by', 'academic_events_creator_fk')
                ->references('id')->on('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['institution_id', 'branch_id'], 'events_inst_branch_idx');
            $table->index(['start_date', 'end_date'], 'events_dates_idx');
            $table->index('status', 'events_status_idx');
        });

        Schema::create('event_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->foreign('event_id', 'academic_event_trans_event_fk')
                ->references('id')->on('events')->cascadeOnDelete();
            $table->string('locale', 10)->index();
            $table->string('name');
            $table->text('description')->nullable();

            $table->unique(['event_id', 'locale'], 'academic_event_trans_unique');
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
