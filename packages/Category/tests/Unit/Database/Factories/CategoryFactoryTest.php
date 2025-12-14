<?php

namespace Packages\Category\Tests\Unit\Database\Factories;

use Illuminate\Support\Str;
use Packages\Category\Models\Category;
use Packages\Category\Tests\TestCase;

class CategoryFactoryTest extends TestCase
{
    public function test_it_creates_a_valid_category(): void
    {
        $category = Category::factory()->create();

        $this->assertInstanceOf(Category::class, $category);

        $this->assertNotEmpty($category->name);
        $this->assertNotEmpty($category->slug);
    }

    public function test_name_starts_with_uppercase_letter(): void
    {
        $category = Category::factory()->create();

        $this->assertSame(
            ucfirst(strtolower($category->name)),
            $category->name
        );
    }

    public function test_slug_is_a_valid_slug(): void
    {
        $category = Category::factory()->create();

        $this->assertSame(
            Str::slug($category->name),
            $category->slug
        );
    }

    public function test_slug_contains_only_slug_characters(): void
    {
        $category = Category::factory()->create();

        $this->assertMatchesRegularExpression(
            '/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            $category->slug
        );
    }

    public function test_factory_creates_unique_categories(): void
    {
        $categories = Category::factory()->count(10)->create();

        $this->assertCount(
            10,
            $categories->pluck('name')->unique()
        );

        $this->assertCount(
            10,
            $categories->pluck('slug')->unique()
        );
    }
}
