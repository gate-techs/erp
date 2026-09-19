<?php

namespace Modules\HR\Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\HR\Enums\HrSettingType;
use Modules\HR\Enums\PenaltyCalculationType;
use Modules\HR\Enums\PenaltyDeductionMethod;
use Modules\HR\Enums\PenaltyStatus;
use Modules\HR\Models\Employee;
use Modules\HR\Models\HrSetting;
use Modules\HR\Models\Penalty;

class PenaltySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $adminId   = $adminUser?->id ?? 1;

        $employees = Employee::where('status', 'active')->orderBy('id')->get();
        if ($employees->isEmpty()) {
            $employees = Employee::orderBy('id')->get();
        }

        if ($employees->isEmpty()) {
            return;
        }

        $penaltyTypes = HrSetting::where('type', HrSettingType::PENALTY_TYPES->value)->get();
        if ($penaltyTypes->isEmpty()) {
            return;
        }

        if (Penalty::count() > 0) {
            return;
        }

        $empCount  = $employees->count();
        $typeCount = $penaltyTypes->count();

        $getEmp  = fn(int $i) => $employees[$i % $empCount];
        $getType = fn(int $i) => $penaltyTypes[$i % $typeCount];

        $now = Carbon::now();

        // 1. Deducted: 1 Day wage deduction for unauthorized absence
        $emp1      = $getEmp(0);
        $type1     = $getType(2); // absence
        $dailyRate1 = round(($emp1->basic_salary ?? 600) / 30, 2);
        Penalty::create([
            'employee_id'       => $emp1->id,
            'penalty_type_id'   => $type1->id,
            'calculation_type'  => PenaltyCalculationType::DAYS_DEDUCTION->value,
            'calculation_value' => 1.0,
            'amount'            => $dailyRate1,
            'penalty_date'      => $now->copy()->subDays(10)->toDateString(),
            'deduction_method'  => PenaltyDeductionMethod::NEXT_PAYROLL->value,
            'payroll_month'     => $now->format('Y-m'),
            'status'            => PenaltyStatus::DEDUCTED->value,
            'reason'            => 'غياب متكرر بدون إذن مسبق أو إخطار للإدارة.',
            'notes'             => 'تم توجيه لفت نظر شفهي مسبقاً قبل تطبيق الخصم.',
            'created_by'        => $adminId,
            'approved_by'       => $adminId,
            'approved_at'       => $now->copy()->subDays(8),
            'deducted_by'       => $adminId,
            'deducted_at'       => $now->copy()->subDays(7),
        ]);

        // 2. Approved: Fixed amount deduction (30 KWD) for unexcused delay
        $emp2  = $getEmp(1);
        $type2 = $getType(1); // delay
        Penalty::create([
            'employee_id'       => $emp2->id,
            'penalty_type_id'   => $type2->id,
            'calculation_type'  => PenaltyCalculationType::FIXED_AMOUNT->value,
            'calculation_value' => 30.00,
            'amount'            => 30.00,
            'penalty_date'      => $now->copy()->subDays(5)->toDateString(),
            'deduction_method'  => PenaltyDeductionMethod::NEXT_PAYROLL->value,
            'payroll_month'     => $now->format('Y-m'),
            'status'            => PenaltyStatus::APPROVED->value,
            'reason'            => 'تأخر عن مواعيد العمل الرسمية لأكثر من 45 دقيقة بدون عذر مقبول.',
            'notes'             => 'جاهز للتطبيق مع مسير رواتب الشهر الحالي.',
            'created_by'        => $adminId,
            'approved_by'       => $adminId,
            'approved_at'       => $now->copy()->subDays(3),
        ]);

        // 3. Pending: Administrative Written Warning (Non-financial)
        $emp3  = $getEmp(2);
        $type3 = $getType(0); // written warning
        Penalty::create([
            'employee_id'       => $emp3->id,
            'penalty_type_id'   => $type3->id,
            'calculation_type'  => PenaltyCalculationType::WARNING_ONLY->value,
            'calculation_value' => null,
            'amount'            => 0.00,
            'penalty_date'      => $now->copy()->subDays(2)->toDateString(),
            'deduction_method'  => PenaltyDeductionMethod::DIRECT_DEDUCTION->value,
            'payroll_month'     => null,
            'status'            => PenaltyStatus::PENDING->value,
            'reason'            => 'إنذار كتابي أول بشأن عدم التقيد بالزي الرسمي المعتمد بالشركة.',
            'notes'             => 'بانتظار توقيع واعتماد المدير المباشر.',
            'created_by'        => $adminId,
        ]);

        // 4. Pending: 2 Days deduction for negligence & safety violation
        $emp4       = $getEmp(3);
        $type4      = $getType(3); // safety violation
        $dailyRate4 = round(($emp4->basic_salary ?? 750) / 30, 2);
        $amount4    = round($dailyRate4 * 2, 2);
        Penalty::create([
            'employee_id'       => $emp4->id,
            'penalty_type_id'   => $type4->id,
            'calculation_type'  => PenaltyCalculationType::DAYS_DEDUCTION->value,
            'calculation_value' => 2.0,
            'amount'            => $amount4,
            'penalty_date'      => $now->toDateString(),
            'deduction_method'  => PenaltyDeductionMethod::NEXT_PAYROLL->value,
            'payroll_month'     => $now->format('Y-m'),
            'status'            => PenaltyStatus::PENDING->value,
            'reason'            => 'مخالفة معايير السلامة المهنية ومغادرة موقع العمل دون إغلاق المعدات.',
            'notes'             => 'بناءً على تقرير مشرف الموقع رقم TR-402.',
            'created_by'        => $adminId,
        ]);
    }
}
