<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('academic_fee_payments')
            ->where('status', 'pending')
            ->update(['status' => 'unpaid']);
    }

    public function down(): void
    {
        DB::table('academic_fee_payments')
            ->where('status', 'unpaid')
            ->update(['status' => 'pending']);
    }
};
