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
        if (!Schema::hasColumn('general_settings', 'status')) {
            Schema::table('general_settings', function (Blueprint $table) {
                $table->boolean('status')->default(true)->after('image');
            });
        }

        if (!Schema::hasColumn('general_setting_translations', 'description')) {
            Schema::table('general_setting_translations', function (Blueprint $table) {
                $table->text('description')->nullable()->after('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('general_settings', 'status')) {
            Schema::table('general_settings', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }

        if (Schema::hasColumn('general_setting_translations', 'description')) {
            Schema::table('general_setting_translations', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
