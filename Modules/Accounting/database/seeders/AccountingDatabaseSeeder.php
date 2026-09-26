<?php

namespace Modules\Accounting\Database\Seeders;

use Illuminate\Database\Seeder;

class AccountingDatabaseSeeder extends Seeder
{
    //===============================
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(EntityDatabaseSeeder::class);
        $this->call(FiscalYearDatabaseSeeder::class);
        $this->call(AccountDatabaseSeeder::class);
        $this->call(LockDateDatabaseSeeder::class);
        $this->call(CostCenterDatabaseSeeder::class);
        $this->call(JournalDatabaseSeeder::class);
        $this->call(CashAccountDatabaseSeeder::class);
        $this->call(JournalEntryDatabaseSeeder::class);
    }
}

