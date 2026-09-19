<?php

namespace Modules\HR\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\HR\Models\Payroll;
use Modules\HR\Services\PayrollService;

class PayrollSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::first();
        $adminId = $admin ? $admin->id : 1;

        // Check if payroll for 2026-09 already exists
        if (Payroll::where('payroll_month', '2026-09')->exists()) {
            return;
        }

        /** @var PayrollService $payrollService */
        $payrollService = app(PayrollService::class);

        try {
            $payrollService->generatePayroll(
                month: '2026-09',
                branchId: null,
                title: 'مسير رواتب شهر سبتمبر 2026',
                notes: 'مسير الرواتب الرئيسي المعتمد لجميع فروع وموظفي الشركة.',
                userId: $adminId
            );
        } catch (\Exception $e) {
            // If already generated or no employees, log or ignore
        }
    }
}
