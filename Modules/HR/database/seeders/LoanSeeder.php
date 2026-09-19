<?php

namespace Modules\HR\Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\HR\Enums\InstallmentStatus;
use Modules\HR\Enums\LoanStatus;
use Modules\HR\Enums\LoanType;
use Modules\HR\Models\Employee;
use Modules\HR\Models\Loan;
use Modules\HR\Models\LoanInstallment;

class LoanSeeder extends Seeder
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

        if (Loan::count() > 0) {
            return;
        }

        $empCount = $employees->count();
        $getEmp = function (int $index) use ($employees, $empCount) {
            return $employees[$index % $empCount];
        };

        $now = Carbon::now();

        // 1. Active Disbursed Loan with 3 installments paid
        $emp1 = $getEmp(0);
        $loan1 = Loan::create([
            'employee_id'            => $emp1->id,
            'created_by'             => $adminId,
            'type'                   => LoanType::INSTALLMENT_LOAN->value,
            'amount'                 => 1200.00,
            'installments_count'     => 12,
            'monthly_installment'    => 100.00,
            'start_date'             => $now->copy()->subMonths(3)->startOfMonth()->format('Y-m-d'),
            'total_paid'             => 300.00,
            'remaining_amount'       => 900.00,
            'status'                 => LoanStatus::DISBURSED->value,
            'reason'                 => 'قرض زواج وتأثيث مسكن جديد مجدول على سنة كاملة.',
            'disbursed_at'           => $now->copy()->subMonths(3)->startOfMonth()->subDays(5),
            'disbursement_method'    => 'bank_transfer',
            'disbursement_reference' => 'TR-9988231',
            'manager_approved_by'    => $adminId,
            'manager_approved_at'    => $now->copy()->subMonths(3)->startOfMonth()->subDays(8),
            'hr_approved_by'         => $adminId,
            'hr_approved_at'         => $now->copy()->subMonths(3)->startOfMonth()->subDays(7),
        ]);

        for ($i = 1; $i <= 12; $i++) {
            $dueDate = $now->copy()->subMonths(3)->addMonths($i - 1)->startOfMonth();
            $isPaid  = ($i <= 3);

            LoanInstallment::create([
                'loan_id'            => $loan1->id,
                'installment_number' => $i,
                'due_date'           => $dueDate->format('Y-m-d'),
                'amount'             => 100.00,
                'paid_amount'        => $isPaid ? 100.00 : 0.00,
                'status'             => $isPaid ? InstallmentStatus::PAID->value : InstallmentStatus::PENDING->value,
                'paid_at'            => $isPaid ? $dueDate->format('Y-m-d') : null,
                'notes'              => $isPaid ? 'خصم مسير الرواتب تلقائياً' : null,
            ]);
        }

        // 2. Active Disbursed Salary Advance (Due this month)
        $emp2 = $getEmp(1);
        $loan2 = Loan::create([
            'employee_id'            => $emp2->id,
            'created_by'             => $adminId,
            'type'                   => LoanType::SALARY_ADVANCE->value,
            'amount'                 => 250.00,
            'installments_count'     => 1,
            'monthly_installment'    => 250.00,
            'start_date'             => $now->copy()->endOfMonth()->format('Y-m-d'),
            'total_paid'             => 0.00,
            'remaining_amount'       => 250.00,
            'status'                 => LoanStatus::DISBURSED->value,
            'reason'                 => 'سلفة طارئة على الراتب لشراء مستلزمات دراسية للأولاد.',
            'disbursed_at'           => $now->copy()->subDays(10),
            'disbursement_method'    => 'cash',
            'disbursement_reference' => 'CASH-REC-104',
            'manager_approved_by'    => $adminId,
            'manager_approved_at'    => $now->copy()->subDays(12),
            'hr_approved_by'         => $adminId,
            'hr_approved_at'         => $now->copy()->subDays(11),
        ]);

        LoanInstallment::create([
            'loan_id'            => $loan2->id,
            'installment_number' => 1,
            'due_date'           => $now->copy()->endOfMonth()->format('Y-m-d'),
            'amount'             => 250.00,
            'paid_amount'        => 0.00,
            'status'             => InstallmentStatus::PENDING->value,
            'notes'              => 'مستحق الخصم في مسير راتب هذا الشهر',
        ]);

        // 3. Fully Repaid Loan
        $emp3 = $getEmp(2 % $empCount);
        $loan3 = Loan::create([
            'employee_id'            => $emp3->id,
            'created_by'             => $adminId,
            'type'                   => LoanType::INSTALLMENT_LOAN->value,
            'amount'                 => 600.00,
            'installments_count'     => 6,
            'monthly_installment'    => 100.00,
            'start_date'             => $now->copy()->subMonths(7)->format('Y-m-d'),
            'total_paid'             => 600.00,
            'remaining_amount'       => 0.00,
            'status'                 => LoanStatus::PAID->value,
            'reason'                 => 'سلفة علاج وسداد مصاريف استشفاء عاجلة.',
            'disbursed_at'           => $now->copy()->subMonths(7)->subDays(2),
            'disbursement_method'    => 'bank_transfer',
            'disbursement_reference' => 'TR-8811223',
            'manager_approved_by'    => $adminId,
            'manager_approved_at'    => $now->copy()->subMonths(7)->subDays(4),
            'hr_approved_by'         => $adminId,
            'hr_approved_at'         => $now->copy()->subMonths(7)->subDays(3),
        ]);

        for ($i = 1; $i <= 6; $i++) {
            $dueDate = $now->copy()->subMonths(7)->addMonths($i - 1)->startOfMonth();
            LoanInstallment::create([
                'loan_id'            => $loan3->id,
                'installment_number' => $i,
                'due_date'           => $dueDate->format('Y-m-d'),
                'amount'             => 100.00,
                'paid_amount'        => 100.00,
                'status'             => InstallmentStatus::PAID->value,
                'paid_at'            => $dueDate->format('Y-m-d'),
                'notes'              => 'تم السداد بنجاح',
            ]);
        }

        // 4. Pending Approval Loan
        $emp4 = $getEmp(3 % $empCount);
        $loan4 = Loan::create([
            'employee_id'         => $emp4->id,
            'created_by'          => $adminId,
            'type'                => LoanType::INSTALLMENT_LOAN->value,
            'amount'              => 800.00,
            'installments_count'  => 8,
            'monthly_installment' => 100.00,
            'start_date'          => $now->copy()->addMonth()->startOfMonth()->format('Y-m-d'),
            'total_paid'          => 0.00,
            'remaining_amount'    => 800.00,
            'status'              => LoanStatus::PENDING->value,
            'reason'              => 'طلب سلفة لشراء أجهزة حاسب آلي وتجهيزات تعليمية للأبناء.',
        ]);

        for ($i = 1; $i <= 8; $i++) {
            $dueDate = $now->copy()->addMonth()->addMonths($i - 1)->startOfMonth();
            LoanInstallment::create([
                'loan_id'            => $loan4->id,
                'installment_number' => $i,
                'due_date'           => $dueDate->format('Y-m-d'),
                'amount'             => 100.00,
                'paid_amount'        => 0.00,
                'status'             => InstallmentStatus::PENDING->value,
            ]);
        }

        // 5. Approved Awaiting Disbursement
        $emp5 = $getEmp(4 % $empCount);
        $loan5 = Loan::create([
            'employee_id'            => $emp5->id,
            'created_by'             => $adminId,
            'type'                   => LoanType::INSTALLMENT_LOAN->value,
            'amount'                 => 1500.00,
            'installments_count'     => 15,
            'monthly_installment'    => 100.00,
            'start_date'             => $now->copy()->addMonth()->startOfMonth()->format('Y-m-d'),
            'total_paid'             => 0.00,
            'remaining_amount'       => 1500.00,
            'status'                 => LoanStatus::APPROVED->value,
            'reason'                 => 'قرض ترميم وصيانة منزلية طارئة.',
            'manager_approved_by'    => $adminId,
            'manager_approved_at'    => $now->copy()->subDay(),
            'hr_approved_by'         => $adminId,
            'hr_approved_at'         => $now->copy()->subHours(5),
        ]);

        for ($i = 1; $i <= 15; $i++) {
            $dueDate = $now->copy()->addMonth()->addMonths($i - 1)->startOfMonth();
            LoanInstallment::create([
                'loan_id'            => $loan5->id,
                'installment_number' => $i,
                'due_date'           => $dueDate->format('Y-m-d'),
                'amount'             => 100.00,
                'paid_amount'        => 0.00,
                'status'             => InstallmentStatus::PENDING->value,
            ]);
        }

        // 6. Rejected Loan Request
        $emp6 = $getEmp(5 % $empCount);
        Loan::create([
            'employee_id'      => $emp6->id,
            'created_by'       => $adminId,
            'type'             => LoanType::INSTALLMENT_LOAN->value,
            'amount'           => 3500.00,
            'installments_count' => 10,
            'monthly_installment' => 350.00,
            'start_date'       => $now->copy()->addMonth()->startOfMonth()->format('Y-m-d'),
            'total_paid'       => 0.00,
            'remaining_amount' => 3500.00,
            'status'           => LoanStatus::REJECTED->value,
            'reason'           => 'طلب سلفة لتغطية نفقات تجارية خاصة.',
            'rejection_reason' => 'تم رفض الطلب لتجاوز القسط الشهري السقف القانوني المسموح به (33% من الراتب الأساسي).',
        ]);
    }
}
