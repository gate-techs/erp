<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Enums\UserType;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    //==================================
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gatetech.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'user_type' => UserType::DEVELOPER,
            ]
        );

        $this->call(PermissionDatabaseSeeder::class);
        $this->call(\Modules\Settings\Database\Seeders\SettingsDatabaseSeeder::class);
        $this->call(\Modules\Nursery\Database\Seeders\NurseryDatabaseSeeder::class);
        $this->call(\Modules\HR\Database\Seeders\HRDatabaseSeeder::class);
        $this->call(\Modules\Accounting\Database\Seeders\AccountingDatabaseSeeder::class);
        $this->call(\Modules\Inventory\Database\Seeders\InventoryDatabaseSeeder::class);
        $this->call(\Modules\Academic\Database\Seeders\AcademicDatabaseSeeder::class);
    }
}

