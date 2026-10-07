<?php

namespace Modules\Academic\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Models\AcademicSubject;
use Modules\Academic\Models\AcademicYear;
use Modules\Nursery\Models\Nursery;

class AcademicSubjectDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Academic Subjects.
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

        $subjectsData = [
            [
                'name_ar'        => 'عربي',
                'name_en'        => 'Arabic',
                'description_ar' => 'منهج اللغة العربية والقرآن الكريم والتربية الدينية وتطوير مهارات القراءة والكتابة',
                'description_en' => 'Arabic language, reading, and foundational writing curriculum',
            ],
            [
                'name_ar'        => 'انجليزي',
                'name_en'        => 'English',
                'description_ar' => 'منهج اللغة الإنجليزية والصوتيات (Phonics) والمحادثة وتنمية المفردات',
                'description_en' => 'English language curriculum, phonics, and communication skills',
            ],
            [
                'name_ar'        => 'رياضيات',
                'name_en'        => 'Mathematics',
                'description_ar' => 'مادة الرياضيات والحساب والأرقام وتنمية التفكير المنطقي',
                'description_en' => 'Mathematics curriculum, basic counting, arithmetic, and logic',
            ],
            [
                'name_ar'        => 'رياضيات انجليزي',
                'name_en'        => 'Math (English)',
                'description_ar' => 'منهج الماث والرياضيات باللغة الإنجليزية للغات والتجريبي',
                'description_en' => 'Math curriculum in English for languages and international tracks',
            ],
            [
                'name_ar'        => 'علوم',
                'name_en'        => 'Science',
                'description_ar' => 'مادة العلوم واستكشاف الطبيعة والبيئة والتجارب العلمية المبسطة',
                'description_en' => 'Science curriculum, nature exploration, and introductory experiments',
            ],
        ];

        foreach ($nurseries as $nursery) {
            $currentAcademicYear = AcademicYear::where('institution_id', $nursery->id)
                ->where('is_current', true)
                ->first()
                ?? AcademicYear::where('institution_id', $nursery->id)->first();

            $stageIds = AcademicStage::where('institution_id', $nursery->id)->pluck('id')->toArray();

            foreach ($subjectsData as $data) {
                // Check if subject already exists for this institution
                $subject = AcademicSubject::where('institution_id', $nursery->id)
                    ->whereHas('translations', function ($query) use ($data) {
                        $query->where('name', $data['name_ar'])
                              ->orWhere('name', $data['name_en']);
                    })
                    ->first();

                if (!$subject) {
                    $subject = new AcademicSubject();
                    $subject->institution_id   = $nursery->id;
                    $subject->academic_year_id = $currentAcademicYear?->id;
                    $subject->status           = AcademicSubject::STATUS_ACTIVE;
                    $subject->created_by       = $adminId;

                    $subject->translateOrNew('ar')->name        = $data['name_ar'];
                    $subject->translateOrNew('ar')->description = $data['description_ar'];

                    $subject->translateOrNew('en')->name        = $data['name_en'];
                    $subject->translateOrNew('en')->description = $data['description_en'];

                    $subject->save();
                } else {
                    $subject->status = AcademicSubject::STATUS_ACTIVE;
                    if (!$subject->academic_year_id && $currentAcademicYear) {
                        $subject->academic_year_id = $currentAcademicYear->id;
                    }

                    $subject->translateOrNew('ar')->name        = $data['name_ar'];
                    $subject->translateOrNew('ar')->description = $data['description_ar'];

                    $subject->translateOrNew('en')->name        = $data['name_en'];
                    $subject->translateOrNew('en')->description = $data['description_en'];

                    $subject->save();
                }

                if (!empty($stageIds)) {
                    $subject->academicStages()->sync($stageIds);
                }
            }
        }
    }
}
