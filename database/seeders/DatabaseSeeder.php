<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Packages\User\Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            SettingsSeeder::class,
            UserSeeder::class,
        ]);
    }
}
