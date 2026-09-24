<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Models\FiscalYear;
use Modules\Accounting\Models\FiscalYearTranslation;

class FiscalYearDatabaseSeeder extends Seeder
{
    //===============================
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        FiscalYearTranslation::truncate();
        FiscalYear::truncate();
        Schema::enableForeignKeyConstraints();

        $entities = Entity::all();

        if ($entities->isEmpty()) {
            return;
        }

        $currentYear = (int) date('Y');
        $startDate = "{$currentYear}-01-01";
        $endDate = "{$currentYear}-12-31";

        foreach ($entities as $entity) {
            $fiscalYear = FiscalYear::create([
                'entity_id'  => $entity->id,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'status'     => 'open',
                'created_by' => $entity->created_by ?? 1,
                'closed_by'  => null,
                'closed_at'  => null,
            ]);

            $fiscalYear->translateOrNew('ar')->name = "السنة المالية {$currentYear}";
            $fiscalYear->translateOrNew('ar')->description = "السنة المالية الحالية لعام {$currentYear}";

            $fiscalYear->translateOrNew('en')->name = "Fiscal Year {$currentYear}";
            $fiscalYear->translateOrNew('en')->description = "Current Fiscal Year for {$currentYear}";

            $fiscalYear->save();
        }
    }
}
