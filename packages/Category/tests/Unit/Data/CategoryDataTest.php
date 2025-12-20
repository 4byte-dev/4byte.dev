<?php

namespace Packages\Category\Tests\Unit\Data;

use Illuminate\Support\Facades\Auth;
use Mockery;
use Mockery\MockInterface;
use Packages\Category\Data\CategoryData;
use Packages\Category\Models\Category;
use Packages\Category\Tests\TestCase;

class CategoryDataTest extends TestCase
{
    public function test_it_can_be_instantiated_with_defaults(): void
    {
        $data = new CategoryData(
            id: 1,
            name: 'Test Category',
            slug: 'test-category',
            followers: 10,
            isFollowing: true
        );

        $this->assertSame(1, $data->id);
        $this->assertSame('Test Category', $data->name);
        $this->assertSame('test-category', $data->slug);
        $this->assertSame(10, $data->followers);
        $this->assertTrue($data->isFollowing);

        $this->assertSame('category', $data->type);
    }

    public function test_it_creates_data_from_model_without_id_by_default(): void
    {
        $category = Category::factory()->create([
            'name' => 'Inertia',
            'slug' => 'inertia',
        ]);

        $categoryData = CategoryData::fromModel($category);

        $this->assertSame(0, $categoryData->id);
        $this->assertSame('Inertia', $categoryData->name);
        $this->assertSame('inertia', $categoryData->slug);
        $this->assertSame('category', $categoryData->type);
    }

    public function test_it_sets_id_when_flag_is_true(): void
    {
        $category = Category::factory()->create([
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);

        $categoryData = CategoryData::fromModel($category, true);

        $this->assertSame($category->id, $categoryData->id);
        $this->assertSame('Laravel', $categoryData->name);
        $this->assertSame('laravel', $categoryData->slug);
        $this->assertSame('category', $categoryData->type);
    }

    public function test_it_uses_model_methods_for_followers_and_follow_state(): void
    {
        $userId = 123;
        Auth::shouldReceive('id')->once()->andReturn($userId);

        /** @var Category|MockInterface $category */
        $category       = Mockery::mock(Category::class)->makePartial();
        $category->id   = 99;
        $category->name = 'PHP';
        $category->slug = 'php';

        $category->shouldReceive('followersCount')
            ->once()
            ->andReturn(42);

        $category->shouldReceive('isFollowedBy')
            ->once()
            ->with($userId)
            ->andReturn(true);

        $data = CategoryData::fromModel($category, true);

        $this->assertSame(99, $data->id);
        $this->assertSame(42, $data->followers);
        $this->assertTrue($data->isFollowing);
    }

    public function test_it_handles_guest_user(): void
    {
        Auth::shouldReceive('id')->once()->andReturn(null);

        /** @var Category|MockInterface $category */
        $category       = Mockery::mock(Category::class)->makePartial();
        $category->id   = 1;
        $category->name = 'Guest';
        $category->slug = 'guest';

        $category->shouldReceive('followersCount')->once()->andReturn(0);
        $category->shouldReceive('isFollowedBy')
            ->once()
            ->with(null)
            ->andReturn(false);

        $data = CategoryData::fromModel($category);

        $this->assertFalse($data->isFollowing);
    }
}
