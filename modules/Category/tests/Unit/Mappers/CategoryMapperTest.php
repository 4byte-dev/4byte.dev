<?php

namespace Modules\Category\Tests\Unit\Mappers;

use Illuminate\Support\Facades\Auth;
use Mockery;
use Mockery\MockInterface;
use Modules\Category\Mappers\CategoryMapper;
use Modules\Category\Models\Category;
use Modules\Category\Models\CategoryProfile;
use Modules\Category\Tests\TestCase;

class CategoryMapperTest extends TestCase
{
    public function test_it_maps_category_to_data_without_id_by_default(): void
    {
        $category = Category::factory()->create([
            'name' => 'Inertia',
            'slug' => 'inertia',
        ]);

        $categoryData = CategoryMapper::toData($category);

        $this->assertSame(0, $categoryData->id);
        $this->assertSame('Inertia', $categoryData->name);
        $this->assertSame('inertia', $categoryData->slug);
        $this->assertSame('category', $categoryData->type);
    }

    public function test_it_maps_category_to_data_with_id(): void
    {
        $category = Category::factory()->create([
            'name' => 'Laravel',
            'slug' => 'laravel',
        ]);

        $categoryData = CategoryMapper::toData($category, true);

        $this->assertSame($category->id, $categoryData->id);
        $this->assertSame('Laravel', $categoryData->name);
        $this->assertSame('laravel', $categoryData->slug);
    }

    public function test_it_maps_category_profile_to_data(): void
    {
        $profile = CategoryProfile::factory()->create();

        $data = CategoryMapper::toProfileData($profile, true);

        $this->assertSame($profile->id, $data->id);
        $this->assertSame($profile->description, $data->description);
        $this->assertSame($profile->color, $data->color);
    }

    public function test_it_uses_model_methods_for_followers_and_follow_state_in_mapping(): void
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

        $data = CategoryMapper::toData($category, true);

        $this->assertSame(99, $data->id);
        $this->assertSame(42, $data->followers);
        $this->assertTrue($data->isFollowing);
    }

    public function test_it_maps_collection_of_categories(): void
    {
        $categories = Category::factory()->count(2)->create();

        $dataCollection = CategoryMapper::collection($categories, true);

        $this->assertCount(2, $dataCollection);
        $this->assertSame($categories[0]->id, $dataCollection[0]->id);
        $this->assertSame($categories[1]->id, $dataCollection[1]->id);
    }
}
