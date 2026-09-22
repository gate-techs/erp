<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Accounting\Models\Entity;
use Modules\Nursery\Models\Nursery;

class EntityDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nursery = Nursery::with('translations')->first();

        if ($nursery) {
            $nameAr = $nursery->translate('ar')?->name ?? 'حضانة استرو';
            $nameEn = $nursery->translate('en')?->name ?? 'Astro Nursery';
            $descAr = $nursery->translate('ar')?->description ?? 'الكيان المحاسبي لحضانة استرو';
            $descEn = $nursery->translate('en')?->description ?? 'Accounting Entity for Astro Nursery';

            $entity = Entity::updateOrCreate(
                ['nursery_id' => $nursery->id],
                [
                    'code'       => 'ENT-0001',
                    'type'       => 'nursery',
                    'currency'   => 'KWD',
                    'status'     => 'active',
                    'created_by' => $nursery->created_by ?? 1,
                ]
            );

            $entity->translateOrNew('ar')->name = $nameAr;
            $entity->translateOrNew('ar')->description = $descAr;

            $entity->translateOrNew('en')->name = $nameEn;
            $entity->translateOrNew('en')->description = $descEn;

            $entity->save();
        }
    }
}
