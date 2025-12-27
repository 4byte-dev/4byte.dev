<?php

namespace Modules\Tag\Tests\Unit\Database\Factories;

use Modules\Category\Models\Category;
use Modules\Tag\Models\Tag;
use Modules\Tag\Models\TagProfile;
use Modules\Tag\Tests\TestCase;

class TagProfileFactoryTest extends TestCase
{
    public function test_it_creates_a_valid_tag_profile(): void
    {
        $profile = TagProfile::factory()->create();

        $this->assertInstanceOf(TagProfile::class, $profile);

        $this->assertNotEmpty($profile->description);

        $this->assertNotEmpty($profile->color);
        $this->assertMatchesRegularExpression(
            '/^#[0-9a-fA-F]{6}$/',
            $profile->color
        );

        $this->assertDatabaseHas('tags', ['id' => $profile->tag_id]);
    }

    public function test_it_creates_profile_for_given_tag(): void
    {
        $tag = Tag::factory()->create();

        $profile = TagProfile::factory()->create([
            'tag_id' => $tag->id,
        ]);

        $this->assertSame($tag->id, $profile->tag_id);
        $this->assertTrue($profile->tag->is($tag));
    }

    public function test_category_id_can_be_null(): void
    {
        $profiles = TagProfile::factory()->count(10)->create();

        $this->assertTrue(
            $profiles->contains(fn (TagProfile $profile) => $profile->categories()->count() === 0)
        );
    }

    public function test_it_can_create_profile_with_category(): void
    {
        $category = Category::factory()->create();

        $profile = TagProfile::factory()->withCategory($category)->create();

        $this->assertCount(1, $profile->categories);
        $this->assertSame($category->id, $profile->categories->first()->id);
        $this->assertTrue($profile->categories->first()->is($category));
        $this->assertDatabaseHas(Category::class, [
            'id' => $profile->categories->first()->id,
        ]);
    }

    public function test_it_can_create_profile_with_multiple_categories(): void
    {
        $count = 3;

        $profile = TagProfile::factory()
            ->withCategories($count)
            ->create();

        $profile->refresh();

        $this->assertCount($count, $profile->categories);
        $this->assertDatabaseCount(
            Category::class,
            $count
        );
    }

    public function test_with_category_can_be_combined_with_with_categories(): void
    {
        $category = Category::factory()->create();

        $profile = TagProfile::factory()
            ->withCategory($category)
            ->withCategories(2)
            ->create();

        $this->assertTrue(
            $profile->categories->contains($category)
        );

        $this->assertCount(3, $profile->categories);
    }

    public function test_factory_creates_profiles_with_valid_foreign_keys(): void
    {
        $profiles = TagProfile::factory()->count(5)->create();

        foreach ($profiles as $profile) {
            $this->assertDatabaseHas('tags', [
                'id' => $profile->tag_id,
            ]);
        }
    }
}
