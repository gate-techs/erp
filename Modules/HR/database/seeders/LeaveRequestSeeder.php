<?php

namespace Modules\HR\Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\HR\Enums\LeaveRequestStatus;
use Modules\HR\Enums\LeaveType;
use Modules\HR\Models\Employee;
use Modules\HR\Models\LeaveRequest;

class LeaveRequestSeeder extends Seeder
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

        $empCount = $employees->count();

        // Helper to safely pick employee by index
        $getEmp = function (int $index) use ($employees, $empCount) {
            return $employees[$index % $empCount];
        };

        // Reference current date for realistic KPI display
        $now = Carbon::now();
        $todayStr = $now->format('Y-m-d');

        $requests = [
            // 1. On Leave Today (Approved annual leave covering today)
            [
                'employee'     => $getEmp(0),
                'replacement'  => $getEmp(1),
                'leave_type'   => LeaveType::ANNUAL,
                'start_date'   => $now->copy()->subDays(2)->format('Y-m-d'),
                'end_date'     => $now->copy()->addDays(3)->format('Y-m-d'),
                'days_count'   => 5,
                'status'       => LeaveRequestStatus::APPROVED,
                'reason'       => 'إجازة سنوية اعتيادية لقضاء عطلة مع العائلة ومتابعة بعض المعاملات الخاصة.',
                'phone'        => '+965 99123456',
                'approved'     => true,
            ],

            // 2. Pending Approval (Casual leave requested recently)
            [
                'employee'     => $getEmp(1),
                'replacement'  => $getEmp(2),
                'leave_type'   => LeaveType::CASUAL,
                'start_date'   => $now->copy()->addDays(2)->format('Y-m-d'),
                'end_date'     => $now->copy()->addDays(3)->format('Y-m-d'),
                'days_count'   => 2,
                'status'       => LeaveRequestStatus::PENDING,
                'reason'       => 'إجازة عارضة لظرف عائلي طارئ يتطلب التواجد خارج المدينة.',
                'phone'        => '+965 98765432',
                'approved'     => false,
            ],

            // 3. Approved This Month (Sick leave completed earlier this month)
            [
                'employee'     => $getEmp(2),
                'replacement'  => $getEmp(3),
                'leave_type'   => LeaveType::SICK,
                'start_date'   => $now->copy()->startOfMonth()->addDays(2)->format('Y-m-d'),
                'end_date'     => $now->copy()->startOfMonth()->addDays(4)->format('Y-m-d'),
                'days_count'   => 3,
                'status'       => LeaveRequestStatus::APPROVED,
                'reason'       => 'إجازة مرضية بناءً على تقرير طبي رسمي مرفق من مستشفى السلام الدولي.',
                'phone'        => '+965 97654321',
                'approved'     => true,
            ],

            // 4. Pending Approval (Annual leave for next week)
            [
                'employee'     => $getEmp(3),
                'replacement'  => $getEmp(0),
                'leave_type'   => LeaveType::ANNUAL,
                'start_date'   => $now->copy()->addDays(7)->format('Y-m-d'),
                'end_date'     => $now->copy()->addDays(14)->format('Y-m-d'),
                'days_count'   => 6,
                'status'       => LeaveRequestStatus::PENDING,
                'reason'       => 'إجازة سنوية مجدولة للسفر وفق خطة الإجازات المعتمدة مسبقاً مع رئيس القسم.',
                'phone'        => '+965 94561234',
                'approved'     => false,
            ],

            // 5. Manager Approved (Waiting for HR final approval)
            [
                'employee'     => $getEmp(4 % $empCount),
                'replacement'  => $getEmp(1),
                'leave_type'   => LeaveType::MARRIAGE,
                'start_date'   => $now->copy()->addDays(5)->format('Y-m-d'),
                'end_date'     => $now->copy()->addDays(10)->format('Y-m-d'),
                'days_count'   => 5,
                'status'       => LeaveRequestStatus::MANAGER_APPROVED,
                'reason'       => 'إجازة زواج مستحقة بموجب لائحة وسياسات العمل الداخلية للشركة.',
                'phone'        => '+965 93214567',
                'approved'     => false,
            ],

            // 6. Rejected Request
            [
                'employee'     => $getEmp(5 % $empCount),
                'replacement'  => null,
                'leave_type'   => LeaveType::CASUAL,
                'start_date'   => $now->copy()->subDays(5)->format('Y-m-d'),
                'end_date'     => $now->copy()->subDays(4)->format('Y-m-d'),
                'days_count'   => 2,
                'status'       => LeaveRequestStatus::REJECTED,
                'reason'       => 'طلب إجازة عارضة غير متزامنة مع تسليم المهام.',
                'phone'        => '+965 91122334',
                'approved'     => false,
                'rejection'    => 'تعارض موعد الإجازة مع موعد تسليم المشروع النهائي وعدم وجود بديل مستلم للمهام.',
            ],

            // 7. Approved This Month (Covering today as well - second employee on leave today)
            [
                'employee'     => $getEmp(6 % $empCount),
                'replacement'  => $getEmp(2),
                'leave_type'   => LeaveType::ANNUAL,
                'start_date'   => $now->copy()->subDays(1)->format('Y-m-d'),
                'end_date'     => $now->copy()->addDays(2)->format('Y-m-d'),
                'days_count'   => 3,
                'status'       => LeaveRequestStatus::APPROVED,
                'reason'       => 'إجازة قصيرة لظروف عائلية تم التنسيق بشأنها مسبقاً وتسليم الأعمال للموظف البديل.',
                'phone'        => '+965 92233445',
                'approved'     => true,
            ],

            // 8. Unpaid Leave Approved Earlier
            [
                'employee'     => $getEmp(7 % $empCount),
                'replacement'  => $getEmp(3),
                'leave_type'   => LeaveType::UNPAID,
                'start_date'   => $now->copy()->startOfMonth()->format('Y-m-d'),
                'end_date'     => $now->copy()->startOfMonth()->addDays(6)->format('Y-m-d'),
                'days_count'   => 5,
                'status'       => LeaveRequestStatus::APPROVED,
                'reason'       => 'إجازة بدون راتب متفق عليها بناءً على طلب الموظف لاستكمال دراسات عليا.',
                'phone'        => '+965 95566778',
                'approved'     => true,
            ],
        ];

        foreach ($requests as $item) {
            $leave = new LeaveRequest();
            $leave->employee_id             = $item['employee']->id;
            $leave->replacement_employee_id = $item['replacement']?->id;
            $leave->leave_type              = $item['leave_type'];
            $leave->start_date              = $item['start_date'];
            $leave->end_date                = $item['end_date'];
            $leave->days_count              = $item['days_count'];
            $leave->status                  = $item['status'];
            $leave->reason                  = $item['reason'];
            $leave->rejection_reason        = $item['rejection'] ?? null;
            $leave->emergency_contact_phone = $item['phone'];
            $leave->created_by              = $adminId;

            if ($item['approved']) {
                $leave->manager_approved_by = $adminId;
                $leave->manager_approved_at = Carbon::parse($item['start_date'])->subDay();
                $leave->hr_approved_by      = $adminId;
                $leave->hr_approved_at      = Carbon::parse($item['start_date'])->subDay();
            } elseif ($item['status'] === LeaveRequestStatus::MANAGER_APPROVED) {
                $leave->manager_approved_by = $adminId;
                $leave->manager_approved_at = $now->copy()->subHours(6);
            }

            $leave->save();
        }
    }
}
