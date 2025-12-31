<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Article\Database\Seeders\ArticleSeeder;
use Modules\Course\Database\Seeders\CourseSeeder;
use Modules\Entry\Database\Seeders\EntrySeeder;
use Modules\User\Database\Seeders\UserSeeder;

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
            ArticleSeeder::class,
            CourseSeeder::class,
            EntrySeeder::class,
        ]);
    }
}
