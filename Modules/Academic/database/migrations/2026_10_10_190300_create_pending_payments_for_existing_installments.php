<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('academic_fee_installments')
            ->orderBy('id')
            ->each(function (object $installment) {
                $hasPayment = DB::table('academic_fee_payments')
                    ->where('academic_fee_installment_id', $installment->id)
                    ->exists();

                if ($hasPayment) {
                    return;
                }

                DB::table('academic_fee_payments')->insert([
                    'academic_student_fee_id' => $installment->academic_student_fee_id,
                    'academic_fee_installment_id' => $installment->id,
                    'amount' => $installment->amount,
                    'payment_date' => $installment->due_date,
                    'invoice_number' => null,
                    'payment_number' => 'PAY-SCHEDULE-' . $installment->id,
                    'transaction_number' => null,
                    'reference_number' => null,
                    'tracking_number' => null,
                    'status' => 'pending',
                    'created_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
    }

    public function down(): void
    {
        DB::table('academic_fee_payments')
            ->where('payment_number', 'like', 'PAY-SCHEDULE-%')
            ->delete();
    }
};
