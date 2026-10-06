<?php

namespace Modules\Academic\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Academic\Models\AcademicClass;
use Modules\Academic\Models\AcademicStage;
use Modules\Academic\Models\AcademicYear;
use Modules\Nursery\Models\Nursery;
use Modules\Nursery\Models\NurseryBranch;

class AcademicClassDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Academic Classes.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $adminId = $adminUser?->id ?? 1;

        // 1. Get Institution / Nursery
        $nursery = Nursery::first();
        if (!$nursery) {
            $nursery = new Nursery();
            $nursery->fill([
                'status'     => 'active',
                'created_by' => $adminId,
            ]);
            $nursery->translateOrNew('ar')->name = 'حضانة استرو';
            $nursery->translateOrNew('en')->name = 'Astro Nursery';
            $nursery->save();
        }
        $institutionId = $nursery->id;

        // 2. Get Academic Year
        $academicYear = AcademicYear::where('status', 'active')->first() ?? AcademicYear::first();
        if (!$academicYear) {
            $academicYear = new AcademicYear();
            $academicYear->fill([
                'institution_id' => $institutionId,
                'code'           => '2026-2027',
                'start_date'     => '2026-09-01',
                'end_date'       => '2027-06-30',
                'is_current'     => true,
                'status'         => 'active',
                'created_by'     => $adminId,
            ]);
            $academicYear->translateOrNew('ar')->name = 'العام الدراسي 2026 - 2027';
            $academicYear->translateOrNew('en')->name = 'Academic Year 2026 - 2027';
            $academicYear->save();
        }
        $academicYearId = $academicYear->id;

        // 3. Get Stages Map by Code
        $stages = AcademicStage::all()->keyBy('code');

        // Stage configurations (Image & Theme Color)
        $stageConfig = [
            'MINITOTS' => [
                'color' => '#03c3ec',
                'image' => 'assets/img/classes/minitots.svg',
            ],
            'TODDLER' => [
                'color' => '#71dd37',
                'image' => 'assets/img/classes/toddlers.svg',
            ],
            'PRE-K' => [
                'color' => '#ffab00',
                'image' => 'assets/img/classes/pre-k.svg',
            ],
            'KG1' => [
                'color' => '#696cff',
                'image' => 'assets/img/classes/kg1.svg',
            ],
            'KG2' => [
                'color' => '#e83e8c',
                'image' => 'assets/img/classes/kg2.svg',
            ],
        ];

        // 4. Locate Branches
        $branchAbuFatira = NurseryBranch::whereTranslationLike('name', '%فطيرة%')
            ->orWhereTranslationLike('name', '%Fatira%')
            ->first() ?? NurseryBranch::find(1);

        $branchAlZahraa = NurseryBranch::whereTranslationLike('name', '%الزهراء%')
            ->orWhereTranslationLike('name', '%Zahra%')
            ->first() ?? NurseryBranch::find(2);

        // =====================================================================
        // Data for Abu Fatira Branch (19 classes)
        // =====================================================================
        $abuFatiraClasses = [
            ['stage' => 'TODDLER',  'name_en' => 'Toddlers- A', 'name_ar' => 'Toddlers- A', 'capacity' => 15],
            ['stage' => 'TODDLER',  'name_en' => 'Toddlers- B', 'name_ar' => 'Toddlers- B', 'capacity' => 15],
            ['stage' => 'TODDLER',  'name_en' => 'Toddlers- C', 'name_ar' => 'Toddlers- C', 'capacity' => 15],
            ['stage' => 'TODDLER',  'name_en' => 'Toddlers- D', 'name_ar' => 'Toddlers- D', 'capacity' => 15],
            ['stage' => 'TODDLER',  'name_en' => 'Toddlers- E', 'name_ar' => 'Toddlers- E', 'capacity' => 15],

            ['stage' => 'PRE-K',    'name_en' => 'Pre-k- A',    'name_ar' => 'Pre-k- A',    'capacity' => 20],
            ['stage' => 'PRE-K',    'name_en' => 'Pre-k- B',    'name_ar' => 'Pre-k- B',    'capacity' => 20],
            ['stage' => 'PRE-K',    'name_en' => 'Pre-k- C',    'name_ar' => 'Pre-k- C',    'capacity' => 20],
            ['stage' => 'PRE-K',    'name_en' => 'Pre-k- D',    'name_ar' => 'Pre-k- D',    'capacity' => 20],
            ['stage' => 'PRE-K',    'name_en' => 'Pre-k- E',    'name_ar' => 'Pre-k- E',    'capacity' => 20],
            ['stage' => 'PRE-K',    'name_en' => 'Pre-k- F',    'name_ar' => 'Pre-k- F',    'capacity' => 20],
            ['stage' => 'PRE-K',    'name_en' => 'Pre-k- G',    'name_ar' => 'Pre-k- G',    'capacity' => 20],

            ['stage' => 'KG1',      'name_en' => 'Kg1- A',      'name_ar' => 'Kg1- A',      'capacity' => 22],
            ['stage' => 'KG1',      'name_en' => 'Kg1- B',      'name_ar' => 'Kg1- B',      'capacity' => 22],
            ['stage' => 'KG1',      'name_en' => 'Kg1- C',      'name_ar' => 'Kg1- C',      'capacity' => 22],
            ['stage' => 'KG1',      'name_en' => 'Kg1- D',      'name_ar' => 'Kg1- D',      'capacity' => 22],

            ['stage' => 'KG2',      'name_en' => 'Kg2- A',      'name_ar' => 'Kg2- A',      'capacity' => 25],
            ['stage' => 'KG2',      'name_en' => 'Kg2- B',      'name_ar' => 'Kg2- B',      'capacity' => 25],

            ['stage' => 'MINITOTS', 'name_en' => 'Minitots',    'name_ar' => 'Minitots',    'capacity' => 12],
        ];

        // =====================================================================
        // Data for Al-Zahraa Branch (9 classes)
        // =====================================================================
        $alZahraaClasses = [
            ['stage' => 'TODDLER',  'name_en' => 'Toddlers- A', 'name_ar' => 'Toddlers- A', 'capacity' => 15],
            ['stage' => 'TODDLER',  'name_en' => 'Toddlers- B', 'name_ar' => 'Toddlers- B', 'capacity' => 15],
            ['stage' => 'TODDLER',  'name_en' => 'Toddlers- C', 'name_ar' => 'Toddlers- C', 'capacity' => 15],

            ['stage' => 'PRE-K',    'name_en' => 'Pre-k- A',    'name_ar' => 'Pre-k- A',    'capacity' => 20],
            ['stage' => 'PRE-K',    'name_en' => 'Pre-k- B',    'name_ar' => 'Pre-k- B',    'capacity' => 20],
            ['stage' => 'PRE-K',    'name_en' => 'Pre-k- C',    'name_ar' => 'Pre-k- C',    'capacity' => 20],

            ['stage' => 'KG1',      'name_en' => 'Kg1- A',      'name_ar' => 'Kg1- A',      'capacity' => 22],

            ['stage' => 'KG2',      'name_en' => 'Kg2- A',      'name_ar' => 'Kg2- A',      'capacity' => 25],

            ['stage' => 'MINITOTS', 'name_en' => 'Minitots',    'name_ar' => 'Minitots',    'capacity' => 12],
        ];

        // 5. Seed Abu Fatira Branch
        if ($branchAbuFatira) {
            $this->seedBranchClasses($institutionId, $branchAbuFatira->id, $academicYearId, $abuFatiraClasses, $stages, $stageConfig, $adminId);
        }

        // 6. Seed Al-Zahraa Branch
        if ($branchAlZahraa) {
            $this->seedBranchClasses($institutionId, $branchAlZahraa->id, $academicYearId, $alZahraaClasses, $stages, $stageConfig, $adminId);
        }
    }

    /**
     * Helper to seed classes for a specific branch.
     */
    private function seedBranchClasses(
        int $institutionId,
        int $branchId,
        int $academicYearId,
        array $classesList,
        $stages,
        array $stageConfig,
        int $adminId
    ): void {
        foreach ($classesList as $item) {
            $stageCode = $item['stage'];
            $stage = $stages->get($stageCode);
            if (!$stage) {
                continue;
            }

            $config = $stageConfig[$stageCode] ?? [
                'color' => '#696cff',
                'image' => null,
            ];

            // Check if class with this name exists for this branch/stage/year
            $existingClass = AcademicClass::where('institution_id', $institutionId)
                ->where('branch_id', $branchId)
                ->where('academic_year_id', $academicYearId)
                ->where('academic_stage_id', $stage->id)
                ->whereHas('translations', function ($query) use ($item) {
                    $query->where('name', $item['name_en'])
                          ->orWhere('name', $item['name_ar']);
                })
                ->first();

            $classModel = $existingClass ?? new AcademicClass();
            $classModel->fill([
                'institution_id'    => $institutionId,
                'branch_id'         => $branchId,
                'academic_year_id'  => $academicYearId,
                'academic_stage_id' => $stage->id,
                'capacity'          => $item['capacity'] ?? 20,
                'color'             => $config['color'],
                'image'             => $config['image'],
                'status'            => 'active',
                'created_by'        => $adminId,
            ]);

            $classModel->translateOrNew('ar')->name = $item['name_ar'];
            $classModel->translateOrNew('ar')->description = "فصل {$item['name_ar']} - مرحلة {$stage->translate('ar')?->name}";

            $classModel->translateOrNew('en')->name = $item['name_en'];
            $classModel->translateOrNew('en')->description = "Class {$item['name_en']} - Stage {$stage->translate('en')?->name}";

            $classModel->save();
        }
    }
}
