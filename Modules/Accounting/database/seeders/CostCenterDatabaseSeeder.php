<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Accounting\Enums\CostCenterStatus;
use Modules\Accounting\Enums\CostCenterType;
use Modules\Accounting\Models\CostCenter;
use Modules\Accounting\Models\CostCenterTranslation;
use Modules\Accounting\Models\Entity;

class CostCenterDatabaseSeeder extends Seeder
{
    //===============================
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        CostCenterTranslation::truncate();
        CostCenter::truncate();
        Schema::enableForeignKeyConstraints();

        $entities = Entity::all();

        if ($entities->isEmpty()) {
            return;
        }

        foreach ($entities as $entity) {
            $branch = $entity->branches()->first();

            // 1. Root / General Center
            $root = CostCenter::create([
                'entity_id'  => $entity->id,
                'parent_id'  => null,
                'branch_id'  => null,
                'code'       => 'CC-100',
                'type'       => CostCenterType::GENERAL,
                'status'     => CostCenterStatus::ACTIVE,
                'created_by' => $entity->created_by ?? 1,
            ]);

            $root->translateOrNew('ar')->name = 'الإدارة العامة';
            $root->translateOrNew('ar')->description = 'مركز التكلفة الرئيسي للإدارة العامة';
            $root->translateOrNew('en')->name = 'General Administration';
            $root->translateOrNew('en')->description = 'Main cost center for general administration';
            $root->save();

            // 2. Child Cost Center
            $costChild = CostCenter::create([
                'entity_id'  => $entity->id,
                'parent_id'  => $root->id,
                'branch_id'  => $branch?->id,
                'code'       => 'CC-110',
                'type'       => CostCenterType::COST,
                'status'     => CostCenterStatus::ACTIVE,
                'created_by' => $entity->created_by ?? 1,
            ]);

            $costChild->translateOrNew('ar')->name = 'قسم العمليات والتشغيل';
            $costChild->translateOrNew('ar')->description = 'مركز تكلفة يختص بنفقات العمليات والتشغيل';
            $costChild->translateOrNew('en')->name = 'Operations & Services';
            $costChild->translateOrNew('en')->description = 'Cost center for operational expenses';
            $costChild->save();

            // 3. Child Profit Center
            $profitChild = CostCenter::create([
                'entity_id'  => $entity->id,
                'parent_id'  => $root->id,
                'branch_id'  => $branch?->id,
                'code'       => 'CC-120',
                'type'       => CostCenterType::PROFIT,
                'status'     => CostCenterStatus::ACTIVE,
                'created_by' => $entity->created_by ?? 1,
            ]);

            $profitChild->translateOrNew('ar')->name = 'قسم المبيعات والإيرادات';
            $profitChild->translateOrNew('ar')->description = 'مركز ربحية لمتابعة الإيرادات والمبيعات';
            $profitChild->translateOrNew('en')->name = 'Sales & Revenue';
            $profitChild->translateOrNew('en')->description = 'Profit center for sales and revenues';
            $profitChild->save();
        }
    }
}
