<?php

namespace Modules\Academic\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Academic\Models\AcademicFeeType;
use Modules\Nursery\Models\Nursery;

class AcademicFeeTypeDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $nurseries = Nursery::all();

        $defaultTypes = [
            [
                'code' => 'TUITION',
                'ar'   => ['name' => 'رسوم دراسية وتعليمية', 'description' => 'الرسوم الأساسية للمنهج والتعليم الدراسي'],
                'en'   => ['name' => 'Tuition Fees', 'description' => 'Main tuition and curriculum fees'],
                'order' => 1,
            ],
            [
                'code' => 'REGISTRATION',
                'ar'   => ['name' => 'رسوم تسجيل وقبول', 'description' => 'رسوم التسجيل والملف للطلاب الجدد'],
                'en'   => ['name' => 'Registration & Admission Fees', 'description' => 'One-time admission and registration fees'],
                'order' => 2,
            ],
            [
                'code' => 'BOOKS',
                'ar'   => ['name' => 'رسوم كتب ومواد تعليمية', 'description' => 'قيمة الكتب والوسائل التعليمية والملازم'],
                'en'   => ['name' => 'Books & Materials Fees', 'description' => 'Study books, stationery and learning aids'],
                'order' => 3,
            ],
            [
                'code' => 'TRANSPORT',
                'ar'   => ['name' => 'رسوم باص ومواصلات', 'description' => 'اشتراك خدمة النقل والمواصلات المدرسية'],
                'en'   => ['name' => 'Transportation Fees', 'description' => 'Bus and transportation service subscription'],
                'order' => 4,
            ],
            [
                'code' => 'UNIFORM',
                'ar'   => ['name' => 'رسوم زي مدرسي', 'description' => 'قيمة الزي الرسمي والرياضي'],
                'en'   => ['name' => 'Uniform Fees', 'description' => 'School and sports uniforms'],
                'order' => 5,
            ],
            [
                'code' => 'ACTIVITIES',
                'ar'   => ['name' => 'رسوم أنشطة ورعاية', 'description' => 'رسوم الرحلات والأنشطة اللاصفية والوجبات'],
                'en'   => ['name' => 'Activities & Care Fees', 'description' => 'Extracurricular activities, trips and care'],
                'order' => 6,
            ],
        ];

        foreach ($defaultTypes as $typeData) {
            $feeType = AcademicFeeType::firstOrNew(['code' => $typeData['code']]);
            $feeType->status = AcademicFeeType::STATUS_ACTIVE;
            $feeType->order = $typeData['order'];
            $feeType->institution_id = $nurseries->first()?->id ?? null;

            $feeType->translateOrNew('ar')->name = $typeData['ar']['name'];
            $feeType->translateOrNew('ar')->description = $typeData['ar']['description'];

            $feeType->translateOrNew('en')->name = $typeData['en']['name'];
            $feeType->translateOrNew('en')->description = $typeData['en']['description'];

            $feeType->save();
        }
    }
}
