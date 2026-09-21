<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
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
            ]
        );

        $this->call(\Modules\AccessControl\Database\Seeders\AccessControlDatabaseSeeder::class);
        $this->call(\Modules\Settings\Database\Seeders\SettingsDatabaseSeeder::class);
        $this->call(\Modules\Nursery\Database\Seeders\NurseryDatabaseSeeder::class);
        $this->call(\Modules\HR\Database\Seeders\HRDatabaseSeeder::class);
    }
}
