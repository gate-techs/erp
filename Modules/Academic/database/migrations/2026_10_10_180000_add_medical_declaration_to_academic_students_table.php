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
        Schema::table('academic_students', function (Blueprint $table) {
            $table->boolean('medical_declaration_accepted')->default(false)->after('photo_consent_notes');
            $table->timestamp('medical_declaration_accepted_at')->nullable()->after('medical_declaration_accepted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_students', function (Blueprint $table) {
            $table->dropColumn(['medical_declaration_accepted', 'medical_declaration_accepted_at']);
        });
    }
};
