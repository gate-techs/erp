<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HR\Enums\ShiftStatus;
use Modules\HR\Models\Shift;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shifts = [
            [
                'code'             => 'SH-MORNING',
                'start_time'       => '07:30:00',
                'end_time'         => '15:30:00',
                'total_hours'      => 8.0,
                'grace_period_in'  => 15,
                'grace_period_out' => 0,
                'checkin_start'    => '06:30:00',
                'checkin_end'      => '09:00:00',
                'checkout_start'   => '15:00:00',
                'checkout_end'     => '18:00:00',
                'is_overnight'     => false,
                'color'            => '#7367f0',
                'status'           => ShiftStatus::ACTIVE->value,
                'ar'               => [
                    'name'        => 'الوردية الصباحية الأساسية',
                    'description' => 'دوام صباحي رسمي لجميع الأقسام والإدارات العامة والموظفين الأساسيين',
                ],
                'en'               => [
                    'name'        => 'Standard Morning Shift',
                    'description' => 'Official regular morning working hours for general departments',
                ],
            ],
            [
                'code'             => 'SH-EVENING',
                'start_time'       => '15:00:00',
                'end_time'         => '23:00:00',
                'total_hours'      => 8.0,
                'grace_period_in'  => 15,
                'grace_period_out' => 10,
                'checkin_start'    => '14:00:00',
                'checkin_end'      => '16:30:00',
                'checkout_start'   => '22:30:00',
                'checkout_end'     => '01:00:00',
                'is_overnight'     => false,
                'color'            => '#ff9f43',
                'status'           => ShiftStatus::ACTIVE->value,
                'ar'               => [
                    'name'        => 'الوردية المسائية',
                    'description' => 'دوام مسائي لخدمة العملاء والاستقبال والأنشطة الإضافية',
                ],
                'en'               => [
                    'name'        => 'Evening Shift',
                    'description' => 'Evening shift for customer service, reception, and extracurricular activities',
                ],
            ],
            [
                'code'             => 'SH-NIGHT',
                'start_time'       => '23:00:00',
                'end_time'         => '07:00:00',
                'total_hours'      => 8.0,
                'grace_period_in'  => 20,
                'grace_period_out' => 15,
                'checkin_start'    => '22:00:00',
                'checkin_end'      => '00:30:00',
                'checkout_start'   => '06:30:00',
                'checkout_end'     => '08:30:00',
                'is_overnight'     => true,
                'color'            => '#28c76f',
                'status'           => ShiftStatus::ACTIVE->value,
                'ar'               => [
                    'name'        => 'الوردية الليلية (ممتدة لليوم التالي)',
                    'description' => 'وردية ليلية لحراسة المنشأة والدعم الفني والمراقبة الليلية تمتد حتى صباح اليوم التالي',
                ],
                'en'               => [
                    'name'        => 'Overnight Shift',
                    'description' => 'Overnight shift for campus security, technical support, and surveillance',
                ],
            ],
            [
                'code'             => 'SH-PART-TIME',
                'start_time'       => '08:00:00',
                'end_time'         => '13:00:00',
                'total_hours'      => 5.0,
                'grace_period_in'  => 10,
                'grace_period_out' => 0,
                'checkin_start'    => '07:30:00',
                'checkin_end'      => '09:00:00',
                'checkout_start'   => '12:45:00',
                'checkout_end'     => '14:30:00',
                'is_overnight'     => false,
                'color'            => '#00cfe8',
                'status'           => ShiftStatus::ACTIVE->value,
                'ar'               => [
                    'name'        => 'وردية الدوام الجزئي / الكادر التعليمي',
                    'description' => 'وردية مخصصة للمعلمين والمعلمات والكوادر بنظام الدوام الجزئي والساعات',
                ],
                'en'               => [
                    'name'        => 'Part-Time / Academic Shift',
                    'description' => 'Dedicated shift for academic staff, teachers, and hourly personnel',
                ],
            ],
            [
                'code'             => 'SH-ON-CALL',
                'start_time'       => '09:00:00',
                'end_time'         => '14:00:00',
                'total_hours'      => 5.0,
                'grace_period_in'  => 30,
                'grace_period_out' => 15,
                'checkin_start'    => '08:30:00',
                'checkin_end'      => '10:30:00',
                'checkout_start'   => '13:30:00',
                'checkout_end'     => '15:30:00',
                'is_overnight'     => false,
                'color'            => '#82868b',
                'status'           => ShiftStatus::INACTIVE->value,
                'ar'               => [
                    'name'        => 'وردية العطلات والمناوبات الطارئة',
                    'description' => 'وردية مؤقتة للمناوبات أثناء العطل الرسمية والمناسبات الخاصة (غير مفعلة حالياً)',
                ],
                'en'               => [
                    'name'        => 'Holiday & On-Call Shift',
                    'description' => 'Temporary shift for emergency coverage during holidays (currently inactive)',
                ],
            ],
        ];

        foreach ($shifts as $shiftData) {
            Shift::updateOrCreate(
                ['code' => $shiftData['code']],
                $shiftData
            );
        }
    }
}
