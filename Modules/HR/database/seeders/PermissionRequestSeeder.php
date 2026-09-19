<?php

namespace Modules\HR\Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Modules\HR\Enums\PermissionRequestStatus;
use Modules\HR\Enums\PermissionType;
use Modules\HR\Models\Employee;
use Modules\HR\Models\PermissionRequest;

class PermissionRequestSeeder extends Seeder
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

        // Avoid duplicate seeding
        if (PermissionRequest::count() > 0) {
            return;
        }

        $empCount = $employees->count();

        $getEmp = function (int $index) use ($employees, $empCount) {
            return $employees[$index % $empCount];
        };

        $now = Carbon::now();

        $seedData = [
            // 1. Approved Late Arrival (this month)
            [
                'employee'     => $getEmp(0),
                'date'         => $now->copy()->subDays(2)->format('Y-m-d'),
                'type'         => PermissionType::LATE_ARRIVAL,
                'from_time'    => '08:00:00',
                'to_time'      => '09:30:00',
                'hours_count'  => 1.5,
                'status'       => PermissionRequestStatus::APPROVED,
                'is_emergency' => false,
                'reason'       => 'مراجعة مصلحة حكومية لتجديد وثائق رسمية عاجلة.',
                'approved'     => true,
            ],

            // 2. Pending Late Arrival (upcoming / today)
            [
                'employee'     => $getEmp(1),
                'date'         => $now->format('Y-m-d'),
                'type'         => PermissionType::LATE_ARRIVAL,
                'from_time'    => '08:00:00',
                'to_time'      => '09:00:00',
                'hours_count'  => 1.0,
                'status'       => PermissionRequestStatus::PENDING,
                'is_emergency' => false,
                'reason'       => 'ظرف طارئ وازدحام مروري بسبب أعمال صيانة الطرق.',
                'approved'     => false,
            ],

            // 3. Approved Early Departure
            [
                'employee'     => $getEmp(2 % $empCount),
                'date'         => $now->copy()->subDays(5)->format('Y-m-d'),
                'type'         => PermissionType::EARLY_DEPARTURE,
                'from_time'    => '14:00:00',
                'to_time'      => '16:00:00',
                'hours_count'  => 2.0,
                'status'       => PermissionRequestStatus::APPROVED,
                'is_emergency' => false,
                'reason'       => 'استئذان مبكر لحضور جلسة طبية مجدولة في المستشفى.',
                'approved'     => true,
            ],

            // 4. Pending During Shift
            [
                'employee'     => $getEmp(3 % $empCount),
                'date'         => $now->copy()->addDay()->format('Y-m-d'),
                'type'         => PermissionType::DURING_SHIFT,
                'from_time'    => '11:00:00',
                'to_time'      => '12:30:00',
                'hours_count'  => 1.5,
                'status'       => PermissionRequestStatus::PENDING,
                'is_emergency' => false,
                'reason'       => 'مغادرة مؤقتة خلال فترة العمل لإنهاء معاملة بنكية خاصة.',
                'approved'     => false,
            ],

            // 5. Approved Emergency Permission
            [
                'employee'     => $getEmp(4 % $empCount),
                'date'         => $now->copy()->subDays(1)->format('Y-m-d'),
                'type'         => PermissionType::EMERGENCY,
                'from_time'    => '13:00:00',
                'to_time'      => '14:30:00',
                'hours_count'  => 1.5,
                'status'       => PermissionRequestStatus::APPROVED,
                'is_emergency' => true,
                'reason'       => 'ظرف عائلي مفاجئ يتطلب الحضور الفوري في المدرسة لمتابعة حالة صحية للأبناء.',
                'approved'     => true,
            ],

            // 6. Rejected Request
            [
                'employee'     => $getEmp(5 % $empCount),
                'date'         => $now->copy()->subDays(4)->format('Y-m-d'),
                'type'         => PermissionType::LATE_ARRIVAL,
                'from_time'    => '08:00:00',
                'to_time'      => '10:00:00',
                'hours_count'  => 2.0,
                'status'       => PermissionRequestStatus::REJECTED,
                'is_emergency' => false,
                'reason'       => 'طلب تأخير في الحضور لظروف شخصية خاصة.',
                'rejection'    => 'تم رفض الطلب نظراً لوجود تسليم عاجل للمهام الصباحية في هذا اليوم.',
                'approved'     => false,
            ],

            // 7. Approved During Shift
            [
                'employee'     => $getEmp(6 % $empCount),
                'date'         => $now->copy()->subDays(7)->format('Y-m-d'),
                'type'         => PermissionType::DURING_SHIFT,
                'from_time'    => '12:00:00',
                'to_time'      => '13:00:00',
                'hours_count'  => 1.0,
                'status'       => PermissionRequestStatus::APPROVED,
                'is_emergency' => false,
                'reason'       => 'إذن مغادرة للمشاركة في اجتماع تنسيقي مع جهة خارجية.',
                'approved'     => true,
            ],

            // 8. Manager Approved (Pending HR approval)
            [
                'employee'     => $getEmp(7 % $empCount),
                'date'         => $now->copy()->addDays(2)->format('Y-m-d'),
                'type'         => PermissionType::EARLY_DEPARTURE,
                'from_time'    => '15:00:00',
                'to_time'      => '16:00:00',
                'hours_count'  => 1.0,
                'status'       => PermissionRequestStatus::MANAGER_APPROVED,
                'is_emergency' => false,
                'reason'       => 'الانصراف مبكراً بساعة واحدة لالتزام دراسي مسائي.',
                'approved'     => false,
                'manager_app'  => true,
            ],
        ];

        foreach ($seedData as $item) {
            $perm = new PermissionRequest();
            $perm->employee_id      = $item['employee']->id;
            $perm->date             = $item['date'];
            $perm->type             = $item['type'];
            $perm->from_time        = $item['from_time'];
            $perm->to_time          = $item['to_time'];
            $perm->hours_count      = $item['hours_count'];
            $perm->status           = $item['status'];
            $perm->is_emergency     = $item['is_emergency'];
            $perm->reason           = $item['reason'];
            $perm->rejection_reason = $item['rejection'] ?? null;
            $perm->created_by       = $adminId;

            if (!empty($item['approved'])) {
                $perm->manager_approved_by = $adminId;
                $perm->manager_approved_at = Carbon::parse($item['date'])->subDay();
                $perm->hr_approved_by      = $adminId;
                $perm->hr_approved_at      = Carbon::parse($item['date'])->subDay();
            } elseif (!empty($item['manager_app'])) {
                $perm->manager_approved_by = $adminId;
                $perm->manager_approved_at = $now->copy()->subHours(4);
            }

            $perm->save();
        }
    }
}
