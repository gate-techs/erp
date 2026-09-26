<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('ai_tasks')->where('code', 'financial_assistant')->exists();

        if (!$exists) {
            DB::table('ai_tasks')->insert([
                'code' => 'financial_assistant',
                'name' => 'Financial Smart Assistant',
                'description' => 'Financial Q&A and screen-by-screen guide for accounting operations.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('ai_tasks')->where('code', 'financial_assistant')->delete();
    }
};
