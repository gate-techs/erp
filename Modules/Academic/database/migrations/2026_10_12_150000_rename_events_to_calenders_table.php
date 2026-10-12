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
        // 1. Drop foreign key from event_translations to events
        Schema::table('event_translations', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropUnique('event_trans_unique');
        });

        // 2. Rename tables
        Schema::rename('events', 'calenders');
        Schema::rename('event_translations', 'calender_translations');

        // 3. Rename event_id to calender_id and re-add constraints in calender_translations
        Schema::table('calender_translations', function (Blueprint $table) {
            $table->renameColumn('event_id', 'calender_id');
        });

        Schema::table('calender_translations', function (Blueprint $table) {
            $table->foreign('calender_id')
                ->references('id')
                ->on('calenders')
                ->cascadeOnDelete();

            $table->unique(['calender_id', 'locale'], 'calender_trans_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calender_translations', function (Blueprint $table) {
            $table->dropForeign(['calender_id']);
            $table->dropUnique('calender_trans_unique');
        });

        Schema::table('calender_translations', function (Blueprint $table) {
            $table->renameColumn('calender_id', 'event_id');
        });

        Schema::rename('calenders', 'events');
        Schema::rename('calender_translations', 'event_translations');

        Schema::table('event_translations', function (Blueprint $table) {
            $table->foreign('event_id')
                ->references('id')
                ->on('events')
                ->cascadeOnDelete();

            $table->unique(['event_id', 'locale'], 'event_trans_unique');
        });
    }
};
