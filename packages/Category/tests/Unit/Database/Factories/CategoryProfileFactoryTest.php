<?php

namespace Packages\Category\Tests\Unit\Database\Factories;

use Packages\Category\Models\Category;
use Packages\Category\Models\CategoryProfile;
use Packages\Category\Tests\TestCase;

class CategoryProfileFactoryTest extends TestCase
{
    public function test_it_creates_a_valid_category_profile(): void
    {
        $profile = CategoryProfile::factory()->create();

        $this->assertInstanceOf(CategoryProfile::class, $profile);

        $this->assertNotEmpty($profile->description);

        $this->assertNotEmpty($profile->color);
        $this->assertMatchesRegularExpression(
            '/^#[0-9a-fA-F]{6}$/',
            $profile->color
        );

        $this->assertDatabaseHas('categories', ['id' => $profile->category_id]);
    }

    public function test_it_creates_profile_for_given_category(): void
    {
        $category = Category::factory()->create();

        $profile = CategoryProfile::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->assertSame($category->id, $profile->category_id);
        $this->assertTrue($profile->category->is($category));
    }

    public function test_factory_creates_profiles_with_valid_foreign_keys(): void
    {
        $profiles = CategoryProfile::factory()->count(5)->create();

        foreach ($profiles as $profile) {
            $this->assertDatabaseHas('categories', [
                'id' => $profile->category_id,
            ]);
        }
    }
}
