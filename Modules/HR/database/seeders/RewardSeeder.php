<?php

namespace Modules\HR\Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\HR\Enums\HrSettingType;
use Modules\HR\Enums\RewardCalculationType;
use Modules\HR\Enums\RewardPaymentMethod;
use Modules\HR\Enums\RewardStatus;
use Modules\HR\Models\Employee;
use Modules\HR\Models\HrSetting;
use Modules\HR\Models\Reward;

class RewardSeeder extends Seeder
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

        $rewardTypes = HrSetting::where('type', HrSettingType::REWARD_TYPES->value)->get();
        if ($rewardTypes->isEmpty()) {
            return;
        }

        if (Reward::count() > 0) {
            return;
        }

        $empCount  = $employees->count();
        $typeCount = $rewardTypes->count();

        $getEmp  = fn(int $i) => $employees[$i % $empCount];
        $getType = fn(int $i) => $rewardTypes[$i % $typeCount];

        $now = Carbon::now();

        // 1. Paid Spot Bonus - Fixed Amount (KWD 350)
        $emp1  = $getEmp(0);
        $type1 = $getType(0);
        Reward::create([
            'employee_id'       => $emp1->id,
            'reward_type_id'    => $type1->id,
            'calculation_type'  => RewardCalculationType::FIXED_AMOUNT->value,
            'calculation_value' => 350.00,
            'amount'            => 350.00,
            'award_date'        => $now->copy()->subDays(15)->format('Y-m-d'),
            'payment_method'    => RewardPaymentMethod::SEPARATE_PAYMENT->value,
            'payroll_month'     => null,
            'status'            => RewardStatus::PAID->value,
            'reason'            => 'مكافأة تميز فوري نظير إنجاز أعمال إقفال الحسابات السنوية بدقة قياسية وقبل الموعد المحدد.',
            'created_by'        => $adminId,
            'approved_by'       => $adminId,
            'approved_at'       => $now->copy()->subDays(14),
            'paid_by'           => $adminId,
            'paid_at'           => $now->copy()->subDays(13),
            'notes'             => 'تم الإيداع المباشر في الحساب البنكي للموظف.',
        ]);

        // 2. Approved Performance Bonus - Percentage of Salary (50%)
        $emp2   = $getEmp(1);
        $type2  = $getType(1);
        $basic2 = (float) ($emp2->basic_salary ?? 800.0);
        $amt2   = round($basic2 * 0.50, 2);
        Reward::create([
            'employee_id'       => $emp2->id,
            'reward_type_id'    => $type2->id,
            'calculation_type'  => RewardCalculationType::PERCENTAGE->value,
            'calculation_value' => 50.00,
            'amount'            => $amt2,
            'award_date'        => $now->copy()->subDays(8)->format('Y-m-d'),
            'payment_method'    => RewardPaymentMethod::NEXT_PAYROLL->value,
            'payroll_month'     => $now->copy()->format('Y-m'),
            'status'            => RewardStatus::APPROVED->value,
            'reason'            => 'حافز الإنتاجية وتحقيق مستهدفات الربع المالي بنسبة 130% متجاوزاً الخطة المقررة.',
            'created_by'        => $adminId,
            'approved_by'       => $adminId,
            'approved_at'       => $now->copy()->subDays(7),
            'notes'             => 'يصرف مع راتب شهر ' . $now->format('F Y'),
        ]);

        // 3. Pending Employee of the Month - Days Wage Multiplier (7 Days)
        $emp3   = $getEmp(2);
        $type3  = $getType(2);
        $basic3 = (float) ($emp3->basic_salary ?? 650.0);
        $rate3  = round($basic3 / 30, 2);
        $amt3   = round($rate3 * 7, 2);
        Reward::create([
            'employee_id'       => $emp3->id,
            'reward_type_id'    => $type3->id,
            'calculation_type'  => RewardCalculationType::DAYS_MULTIPLIER->value,
            'calculation_value' => 7.00,
            'amount'            => $amt3,
            'award_date'        => $now->copy()->subDays(2)->format('Y-m-d'),
            'payment_method'    => RewardPaymentMethod::NEXT_PAYROLL->value,
            'payroll_month'     => $now->copy()->format('Y-m'),
            'status'            => RewardStatus::PENDING->value,
            'reason'            => 'ترشيح لجائزة موظف الشهر نظير الالتزام التام وأعلى معدل رضا أولياء الأمور.',
            'created_by'        => $adminId,
        ]);

        // 4. Pending Holiday Grant - Fixed Amount (KWD 200)
        $emp4  = $getEmp(3);
        $type4 = $getType(3);
        Reward::create([
            'employee_id'       => $emp4->id,
            'reward_type_id'    => $type4->id,
            'calculation_type'  => RewardCalculationType::FIXED_AMOUNT->value,
            'calculation_value' => 200.00,
            'amount'            => 200.00,
            'award_date'        => $now->copy()->format('Y-m-d'),
            'payment_method'    => RewardPaymentMethod::SEPARATE_PAYMENT->value,
            'payroll_month'     => null,
            'status'            => RewardStatus::PENDING->value,
            'reason'            => 'منحة مناسبات تشجيعية وتقديرية للأداء المتميز في الفعاليات والأنشطة العامة.',
            'created_by'        => $adminId,
        ]);

        // 5. Rejected Candidate Referral Reward
        $emp5  = $getEmp(4);
        $type5 = $getType(4);
        Reward::create([
            'employee_id'       => $emp5->id,
            'reward_type_id'    => $type5->id,
            'calculation_type'  => RewardCalculationType::FIXED_AMOUNT->value,
            'calculation_value' => 150.00,
            'amount'            => 150.00,
            'award_date'        => $now->copy()->subDays(20)->format('Y-m-d'),
            'payment_method'    => RewardPaymentMethod::SEPARATE_PAYMENT->value,
            'payroll_month'     => null,
            'status'            => RewardStatus::REJECTED->value,
            'reason'            => 'مكافأة استقطاب وتزكية مرشح وظيفي جديد.',
            'rejection_reason'  => 'المرشح لم يكمل فترة التجربة المقررة (3 أشهر) وفقاً لسياسة التوظيف المعتمدة.',
            'created_by'        => $adminId,
            'rejected_by'       => $adminId,
            'rejected_at'       => $now->copy()->subDays(18),
        ]);
    }
}
