<?php

namespace Modules\Accounting\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Services\ChartOfAccountsTemplateService;

class AccountDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $entities = Entity::all();
        if ($entities->isEmpty()) {
            $this->command?->warn('No entities found. Running EntityDatabaseSeeder first...');
            $this->call(EntityDatabaseSeeder::class);
            $entities = Entity::all();
        }

        if ($entities->isEmpty()) {
            $this->command?->error('No entities available to seed Chart of Accounts.');
            return;
        }

        $adminUser = User::first();
        $userId = $adminUser?->id;

        $coaService = app(ChartOfAccountsTemplateService::class);
        $excelPath = $coaService->getDefaultExcelPath();

        foreach ($entities as $entity) {
            $this->command?->info("Seeding Chart of Accounts for entity [{$entity->id}: {$entity->name}]...");

            if (file_exists($excelPath)) {
                $this->command?->line(" -> Loading accounts from Excel: <comment>{$excelPath}</comment>");
                $result = $coaService->generateFromExcelForEntity($entity, $excelPath, $userId, true);
                $this->command?->info(" -> Done: {$result['created']} created, {$result['updated']} updated, {$result['total']} total accounts seeded successfully.");
            } else {
                $this->command?->warn(" -> Excel file not found. Falling back to built-in template.");
                $count = $coaService->generateTemplateForEntity($entity, $userId, true);
                $this->command?->info(" -> Done: {$count} accounts seeded from built-in template.");
            }
        }
    }
}
