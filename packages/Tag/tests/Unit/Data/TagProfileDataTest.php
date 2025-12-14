<?php

namespace Packages\Tag\Tests\Unit\Data;

use Packages\Category\Data\CategoryData;
use Packages\Category\Models\Category;
use Packages\Tag\Data\TagProfileData;
use Packages\Tag\Models\TagProfile;
use Packages\Tag\Tests\TestCase;

class TagProfileDataTest extends TestCase
{
    public function test_it_can_be_instantiated_with_valid_data(): void
    {
        $categories = [
            new CategoryData(id: 1, name: 'Backend', slug: 'backend', followers: 5, isFollowing: true),
        ];

        $data = new TagProfileData(
            id: 1,
            description: 'Test Description',
            color: '#ffffff',
            categories: $categories
        );

        $this->assertSame(1, $data->id);
        $this->assertSame('Test Description', $data->description);
        $this->assertSame('#ffffff', $data->color);
        $this->assertCount(1, $data->categories);
        $this->assertInstanceOf(CategoryData::class, $data->categories[0]);
        $this->assertSame("Backend", $data->categories[0]->name);
        $this->assertSame("backend", $data->categories[0]->slug);
        $this->assertSame(5, $data->categories[0]->followers);
        $this->assertSame(true, $data->categories[0]->isFollowing);
    }

    public function test_it_creates_data_from_model_with_empty_categories(): void
    {
        $profile = TagProfile::factory()->create();
        $profile->categories()->detach();

        $data = TagProfileData::fromModel($profile);

        $this->assertSame(0, $data->id);
        $this->assertSame($profile->description, $data->description);
        $this->assertSame($profile->color, $data->color);
        $this->assertCount(0, $data->categories);
    }

    public function test_it_sets_id_when_flag_is_true(): void
    {
        $profile = TagProfile::factory()->create();

        $data = TagProfileData::fromModel($profile, true);

        $this->assertSame($profile->id, $data->id);
        $this->assertSame($profile->description, $data->description);
        $this->assertSame($profile->color, $data->color);
        $this->assertCount(0, $data->categories);
    }

    public function test_it_maps_categories_to_category_data_objects(): void
    {
        $profile = TagProfile::factory()->create();

        $categories = Category::factory()->count(3)->create();

        $profile->categories()->sync($categories->pluck('id'));

        $data = TagProfileData::fromModel($profile);

        $this->assertCount(3, $data->categories);

        foreach ($data->categories as $index => $categoryData) {
            $this->assertInstanceOf(CategoryData::class, $categoryData);
            $this->assertSame($categories[$index]->name, $categoryData->name);
            $this->assertSame($categories[$index]->slug, $categoryData->slug);
        }
    }

    public function test_it_creates_complete_profile_data_with_id_and_categories(): void
    {
        $profile = TagProfile::factory()->create();

        $categories = Category::factory()->count(2)->create();
        $profile->categories()->sync($categories->pluck('id'));

        $data = TagProfileData::fromModel($profile, true);

        $this->assertSame($profile->id, $data->id);
        $this->assertCount(2, $data->categories);
    }
}
