<?php

namespace Modules\Accounting\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Accounting\Models\Entity;
use Modules\Accounting\Models\LockDate;
use Modules\Accounting\Models\LockException;

class LockDateDatabaseSeeder extends Seeder
{
    //===============================
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        LockException::truncate();
        LockDate::truncate();
        Schema::enableForeignKeyConstraints();

        $entities = Entity::all();

        if ($entities->isEmpty()) {
            return;
        }

        $adminUser = User::first();
        $adminUserId = $adminUser?->id ?? 1;
        $previousYearEnd = (date('Y') - 1) . '-12-31';

        foreach ($entities as $entity) {
            LockDate::create([
                'entity_id'             => $entity->id,
                'lock_all_date'         => $previousYearEnd,
                'tax_lock_date'         => $previousYearEnd,
                'fiscal_year_lock_date' => $previousYearEnd,
                'hard_lock_date'        => null,
                'updated_by'            => $entity->created_by ?? $adminUserId,
            ]);

            if ($adminUser) {
                LockException::create([
                    'entity_id'   => $entity->id,
                    'user_id'     => $adminUser->id,
                    'lock_type'   => LockException::TYPE_LOCK_ALL,
                    'valid_from'  => now()->startOfDay(),
                    'valid_until' => now()->addDays(7)->endOfDay(),
                    'reason'      => 'استثناء معتمد لتسوية قيود إقفال الفترة السابقة',
                    'created_by'  => $adminUserId,
                ]);
            }
        }
    }
}
