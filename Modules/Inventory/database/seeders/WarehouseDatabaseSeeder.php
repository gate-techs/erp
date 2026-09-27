<?php

namespace Modules\Inventory\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Accounting\Models\Entity;
use Modules\Inventory\Models\Warehouse;
use Modules\Nursery\Models\NurseryBranch;

class WarehouseDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds for main warehouse and branch warehouses.
     */
    public function run(): void
    {
        $adminUser = User::first();
        $adminId = $adminUser?->id ?? 1;

        // Retrieve all accounting entities
        $entities = Entity::with(['translations'])->get();

        if ($entities->isEmpty()) {
            $this->command?->warn('No accounting entities found. Skipping WarehouseDatabaseSeeder.');
            return;
        }

        foreach ($entities as $entity) {
            $entityNameAr = $entity->translate('ar')?->name ?? 'الكيان المحاسبي الرئيسي';
            $entityNameEn = $entity->translate('en')?->name ?? 'Main Accounting Entity';

            // 1. Create or Update Main Warehouse (مخزن رئيسي)
            $mainWarehouse = Warehouse::where('entity_id', $entity->id)
                ->where('type', 'main')
                ->first();

            $mainCode = $entity->id === 1 ? 'WH-MAIN' : 'WH-MAIN-' . $entity->id;

            if (!$mainWarehouse) {
                // Ensure code uniqueness if code already exists under different record
                if (Warehouse::where('code', $mainCode)->exists()) {
                    $mainCode = 'WH-MAIN-' . $entity->id . '-' . time();
                }

                $mainWarehouse = new Warehouse();
                $mainWarehouse->fill([
                    'entity_id'   => $entity->id,
                    'branch_id'  => null,
                    'code'       => $mainCode,
                    'type'       => 'main',
                    'status'     => 'active',
                    'created_by' => $adminId,
                ]);
            } else {
                $mainWarehouse->branch_id = null;
                $mainWarehouse->type = 'main';
                $mainWarehouse->status = 'active';
            }

            $mainWarehouse->translateOrNew('ar')->name = 'المخزن الرئيسي';
            $mainWarehouse->translateOrNew('ar')->description = 'المخزن المركزي الرئيسي للبضائع والمستلزمات - ' . $entityNameAr;

            $mainWarehouse->translateOrNew('en')->name = 'Main Warehouse';
            $mainWarehouse->translateOrNew('en')->description = 'Central main warehouse for goods and supplies - ' . $entityNameEn;

            $mainWarehouse->save();

            // 2. Create or Update Branch Warehouse for each branch of this entity (مخزن لكل فرع)
            $branches = NurseryBranch::where('nursery_id', $entity->nursery_id)
                ->with('translations')
                ->get();

            foreach ($branches as $branch) {
                $branchWarehouse = Warehouse::where('entity_id', $entity->id)
                    ->where('branch_id', $branch->id)
                    ->first();

                $branchCode = 'WH-BR-' . str_pad($branch->id, 3, '0', STR_PAD_LEFT);

                if (!$branchWarehouse) {
                    if (Warehouse::where('code', $branchCode)->exists()) {
                        $branchCode = 'WH-BR-' . $branch->id . '-' . $entity->id;
                    }

                    $branchWarehouse = new Warehouse();
                    $branchWarehouse->fill([
                        'entity_id'   => $entity->id,
                        'branch_id'  => $branch->id,
                        'code'       => $branchCode,
                        'type'       => 'branch',
                        'status'     => 'active',
                        'created_by' => $adminId,
                    ]);
                } else {
                    $branchWarehouse->type = 'branch';
                    $branchWarehouse->status = 'active';
                }

                $branchNameAr = $branch->translate('ar')?->name ?? $branch->name;
                $branchNameEn = $branch->translate('en')?->name ?? $branch->name;

                $branchWarehouse->translateOrNew('ar')->name = 'مخزن ' . $branchNameAr;
                $branchWarehouse->translateOrNew('ar')->description = 'المخزن الفرعي الخاص بـ ' . $branchNameAr;

                $branchWarehouse->translateOrNew('en')->name = $branchNameEn . ' Warehouse';
                $branchWarehouse->translateOrNew('en')->description = 'Branch warehouse for ' . $branchNameEn;

                $branchWarehouse->save();
            }
        }
    }
}
