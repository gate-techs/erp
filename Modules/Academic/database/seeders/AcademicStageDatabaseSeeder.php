<?php

namespace Modules\Academic\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Academic\Models\AcademicStage;
use Modules\Nursery\Models\Nursery;

class AcademicStageDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Academic Stages.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $adminId = $adminUser?->id ?? 1;

        // Get all nurseries or create a default one if none exists
        $nurseries = Nursery::all();

        if ($nurseries->isEmpty()) {
            $defaultNursery = new Nursery();
            $defaultNursery->fill([
                'status'     => 'active',
                'created_by' => $adminId,
            ]);
            $defaultNursery->translateOrNew('ar')->name = 'حضانة استرو';
            $defaultNursery->translateOrNew('en')->name = 'Astro Nursery';
            $defaultNursery->save();
            $nurseries = collect([$defaultNursery]);
        }

        $stagesData = [
            [
                'code'           => 'MINITOTS',
                'name_ar'        => 'ميني توتس (Minitots)',
                'name_en'        => 'Minitots',
                'min_age_months' => 6,
                'max_age_months' => 18,
                'description_ar' => 'مرحلة الرضع والأطفال دون المشي (من 6 إلى 18 شهراً)',
                'description_en' => 'Infants and young toddlers stage (6 to 18 months)',
            ],
            [
                'code'           => 'TODDLER',
                'name_ar'        => 'تودلر (Toddler)',
                'name_en'        => 'Toddler',
                'min_age_months' => 18,
                'max_age_months' => 36,
                'description_ar' => 'مرحلة الأطفال المشاة (من سنة ونصف إلى 3 سنوات)',
                'description_en' => 'Toddlers stage (18 to 36 months)',
            ],
            [
                'code'           => 'PRE-K',
                'name_ar'        => 'ما قبل الروضة (Pre-K)',
                'name_en'        => 'Pre-k',
                'min_age_months' => 36,
                'max_age_months' => 48,
                'description_ar' => 'مرحلة التهيئة لما قبل الروضة (من 3 إلى 4 سنوات)',
                'description_en' => 'Pre-kindergarten stage (3 to 4 years)',
            ],
            [
                'code'           => 'KG1',
                'name_ar'        => 'الروضة الأولى (KG1)',
                'name_en'        => 'KG1',
                'min_age_months' => 48,
                'max_age_months' => 60,
                'description_ar' => 'مرحلة رياض الأطفال المستوى الأول (من 4 إلى 5 سنوات)',
                'description_en' => 'Kindergarten Level 1 stage (4 to 5 years)',
            ],
            [
                'code'           => 'KG2',
                'name_ar'        => 'الروضة الثانية (KG2)',
                'name_en'        => 'KG2',
                'min_age_months' => 60,
                'max_age_months' => 72,
                'description_ar' => 'مرحلة رياض الأطفال المستوى الثاني (من 5 إلى 6 سنوات)',
                'description_en' => 'Kindergarten Level 2 stage (5 to 6 years)',
            ],
        ];

        foreach ($nurseries as $nursery) {
            foreach ($stagesData as $data) {
                $stage = AcademicStage::firstOrNew([
                    'institution_id' => $nursery->id,
                    'code'           => $data['code'],
                ]);

                $stage->min_age_months = $data['min_age_months'];
                $stage->max_age_months = $data['max_age_months'];
                $stage->status         = AcademicStage::STATUS_ACTIVE;
                $stage->created_by     = $adminId;

                $stage->translateOrNew('ar')->name        = $data['name_ar'];
                $stage->translateOrNew('ar')->description = $data['description_ar'];

                $stage->translateOrNew('en')->name        = $data['name_en'];
                $stage->translateOrNew('en')->description = $data['description_en'];

                $stage->save();
            }
        }
    }
}
