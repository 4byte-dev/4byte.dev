<?php

namespace Packages\Category\Tests\Feature\Database\Seeders;

use Packages\Category\Database\Seeders\CategorySeeder;
use Packages\Category\Models\Category;
use Packages\Category\Models\CategoryProfile;
use Packages\Category\Tests\TestCase;

class CategorySeederTest extends TestCase
{
    public function test_it_seeds_categories(): void
    {
        $this->seed(CategorySeeder::class);

        $this->assertDatabaseCount(Category::class, 10);

        $this->assertDatabaseCount(CategoryProfile::class, 10);

        Category::all()->each(function (Category $category) {
            $this->assertNotNull(
                $category->profile,
            );
        });

        CategoryProfile::all()->each(function (CategoryProfile $profile) {
            $this->assertDatabaseHas(Category::class, [
                'id' => $profile->category_id,
            ]);
        });

        $this->assertEquals(
            10,
            Category::has('profile')->count(),
        );

        $this->assertEquals(
            Category::count(),
            CategoryProfile::count()
        );
    }
}
