<?php

namespace Modules\Academic\Database\Seeders;

use Illuminate\Database\Seeder;

class AcademicDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for Academic Module.
     */
    public function run(): void
    {
        $this->call([
            AcademicStageDatabaseSeeder::class,
            AcademicClassDatabaseSeeder::class,
            AcademicSubjectDatabaseSeeder::class,
            AcademicLessonDatabaseSeeder::class,
            AcademicWeeklyPlanDatabaseSeeder::class,
            AcademicMonthlyPlanDatabaseSeeder::class,
            AcademicGuardianAndStudentsSeeder::class,
        ]);
    }
}

