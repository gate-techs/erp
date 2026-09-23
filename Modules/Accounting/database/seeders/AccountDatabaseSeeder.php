<?php

namespace Modules\Accounting\Database\Seeders;

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
            return;
        }

        $coaService = app(ChartOfAccountsTemplateService::class);

        foreach ($entities as $entity) {
            $coaService->generateTemplateForEntity($entity);
        }
    }
}
