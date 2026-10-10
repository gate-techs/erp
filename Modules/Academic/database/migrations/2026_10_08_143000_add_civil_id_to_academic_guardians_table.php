<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //------------------------------------------
    public function up(): void
    {
        Schema::table('academic_guardians', function (Blueprint $table) {
            $table->string('civil_id', 30)->nullable()->index()->after('guardian_code');
        });
    }

    //------------------------------------------
    public function down(): void
    {
        Schema::table('academic_guardians', function (Blueprint $table) {
            $table->dropColumn('civil_id');
        });
    }
};
