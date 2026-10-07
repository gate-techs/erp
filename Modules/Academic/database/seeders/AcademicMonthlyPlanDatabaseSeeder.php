<?php

namespace Modules\Academic\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Models\AcademicMonthlyPlan;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Models\AcademicYear;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;

class AcademicMonthlyPlanDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $institution = Nursery::first();
        if (! $institution) {
            return;
        }

        $branch = NurseryBranch::where('nursery_id', $institution->id)->first();
        $year = AcademicYear::where('institution_id', $institution->id)->first();
        $stage = AcademicStage::where('institution_id', $institution->id)->first();
        $classes = AcademicClass::where('institution_id', $institution->id)->take(2)->get();

        $plans = [
            [
                'institution_id'    => $institution->id,
                'branch_id'         => $branch?->id,
                'academic_year_id'  => $year?->id,
                'academic_stage_id' => $stage?->id,
                'start_date'        => now()->startOfMonth()->toDateString(),
                'end_date'          => now()->endOfMonth()->toDateString(),
                'status'            => 'active',
                'ar' => [
                    'title'       => 'خطة شهر أكتوبر الأكاديمية والأنشطة',
                    'description' => 'الخطة الشاملة لشهر أكتوبر تتضمن تعليم الحروف والأرقام والأنشطة الحركية والتربوية والمهارات الاستكشافية للأطفال.',
                ],
                'en' => [
                    'title'       => 'October Academic & Activity Plan',
                    'description' => 'Comprehensive October monthly plan covering basic letters, numbers, motor skills, and exploratory activities for children.',
                ],
            ],
            [
                'institution_id'    => $institution->id,
                'branch_id'         => $branch?->id,
                'academic_year_id'  => $year?->id,
                'academic_stage_id' => $stage?->id,
                'start_date'        => now()->addMonth()->startOfMonth()->toDateString(),
                'end_date'          => now()->addMonth()->endOfMonth()->toDateString(),
                'status'            => 'active',
                'ar' => [
                    'title'       => 'خطة شهر نوفمبر التعليمية والمهاراتية',
                    'description' => 'الخطة الشهرية لنوفمبر للتركيز على مهارات التعبير والأنشطة العلمية البسيطة والرياضات الجماعية.',
                ],
                'en' => [
                    'title'       => 'November Educational & Skills Plan',
                    'description' => 'November monthly curriculum focusing on expression skills, simple science activities, and team play.',
                ],
            ],
        ];

        foreach ($plans as $planData) {
            $ar = $planData['ar'];
            $en = $planData['en'];
            unset($planData['ar'], $planData['en']);

            $plan = AcademicMonthlyPlan::create(array_merge($planData, [
                'ar' => $ar,
                'en' => $en,
            ]));

            if ($classes->isNotEmpty()) {
                $plan->classes()->sync($classes->pluck('id')->toArray());
            }
        }
    }
}
