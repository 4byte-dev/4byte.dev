<?php

namespace Modules\Category\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Category\Models\Category;
use Modules\Category\Models\CategoryProfile;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::factory(10)->create()->each(function ($category) {
            CategoryProfile::factory()->for($category)->create();
        });
    }
}
