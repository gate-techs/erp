<?php

namespace Modules\HR\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\HR\Enums\OfficialHolidayStatus;
use Modules\HR\Models\OfficialHoliday;

class OfficialHolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $holidays = [
            [
                'start_date'   => '2026-01-01',
                'end_date'     => '2026-01-01',
                'days_count'   => 1,
                'year'         => 2026,
                'is_recurring' => true,
                'branch_id'    => null,
                'color'        => '#00cfe8',
                'status'       => OfficialHolidayStatus::ACTIVE->value,
                'ar'           => [
                    'name'        => 'عطلة رأس السنة الميلادية',
                    'description' => 'عطلة رسمية سنوية بمناسبة بداية العام الميلادي الجديد',
                ],
                'en'           => [
                    'name'        => 'New Year Holiday',
                    'description' => 'Annual public holiday celebrating the New Year',
                ],
            ],
            [
                'start_date'   => '2026-02-25',
                'end_date'     => '2026-02-26',
                'days_count'   => 2,
                'year'         => 2026,
                'is_recurring' => true,
                'branch_id'    => null,
                'color'        => '#ff9f43',
                'status'       => OfficialHolidayStatus::ACTIVE->value,
                'ar'           => [
                    'name'        => 'عطلة العيد الوطني ويوم التحرير',
                    'description' => 'عطلة رسمية لجميع الفروع والإدارات بمناسبة العيد الوطني',
                ],
                'en'           => [
                    'name'        => 'National & Liberation Day',
                    'description' => 'Official holiday celebrating National and Liberation Day',
                ],
            ],
            [
                'start_date'   => '2026-03-20',
                'end_date'     => '2026-03-23',
                'days_count'   => 4,
                'year'         => 2026,
                'is_recurring' => false,
                'branch_id'    => null,
                'color'        => '#28c76f',
                'status'       => OfficialHolidayStatus::ACTIVE->value,
                'ar'           => [
                    'name'        => 'عطلة عيد الفطر المبارك',
                    'description' => 'إجازة رسمية لجميع الموظفين بمناسبة حلول عيد الفطر المبارك',
                ],
                'en'           => [
                    'name'        => 'Eid Al-Fitr Holiday',
                    'description' => 'Official company holiday celebrating Eid Al-Fitr',
                ],
            ],
            [
                'start_date'   => '2026-05-27',
                'end_date'     => '2026-05-31',
                'days_count'   => 5,
                'year'         => 2026,
                'is_recurring' => false,
                'branch_id'    => null,
                'color'        => '#7367f0',
                'status'       => OfficialHolidayStatus::ACTIVE->value,
                'ar'           => [
                    'name'        => 'عطلة عيد الأضحى المبارك ووقفة عرفات',
                    'description' => 'إجازة رسمية بمناسبة وقفة عرفات وعيد الأضحى المبارك',
                ],
                'en'           => [
                    'name'        => 'Eid Al-Adha & Arafat Day Holiday',
                    'description' => 'Official public holiday for Arafat Day and Eid Al-Adha',
                ],
            ],
            [
                'start_date'   => '2026-06-16',
                'end_date'     => '2026-06-16',
                'days_count'   => 1,
                'year'         => 2026,
                'is_recurring' => false,
                'branch_id'    => null,
                'color'        => '#ea5455',
                'status'       => OfficialHolidayStatus::ACTIVE->value,
                'ar'           => [
                    'name'        => 'عطلة رأس السنة الهجرية',
                    'description' => 'عطلة رسمية بمناسبة بداية العام الهجري الجديد',
                ],
                'en'           => [
                    'name'        => 'Islamic New Year Holiday',
                    'description' => 'Official public holiday for the Hijri New Year',
                ],
            ],
        ];

        foreach ($holidays as $holidayData) {
            $ar = $holidayData['ar'];
            $en = $holidayData['en'];
            unset($holidayData['ar'], $holidayData['en']);

            $holiday = OfficialHoliday::updateOrCreate(
                [
                    'start_date' => $holidayData['start_date'],
                    'end_date'   => $holidayData['end_date'],
                ],
                $holidayData
            );

            $holiday->translateOrNew('ar')->name = $ar['name'];
            $holiday->translateOrNew('ar')->description = $ar['description'];

            $holiday->translateOrNew('en')->name = $en['name'];
            $holiday->translateOrNew('en')->description = $en['description'];

            $holiday->save();
        }
    }
}
