<?php

namespace Packages\Category\Tests\Unit\Models;

use Packages\Article\Models\Article;
use Packages\Category\Models\Category;
use Packages\Category\Models\CategoryProfile;
use Packages\Category\Tests\TestCase;
use Spatie\Activitylog\Models\Activity;

class CategoryTest extends TestCase
{
    public function test_fillable_attributes_are_correct(): void
    {
        $category = new Category();

        $this->assertSame(
            ['name', 'slug'],
            $category->getFillable()
        );
    }

    public function test_it_can_be_created_via_factory(): void
    {
        $category = Category::factory()->create();

        $this->assertInstanceOf(Category::class, $category);
        $this->assertNotEmpty($category->name);
        $this->assertNotEmpty($category->slug);
    }

    public function test_it_can_be_mass_assigned(): void
    {
        $data = [
            'name' => 'Laravel',
            'slug' => 'laravel',
        ];

        $category = Category::create($data);

        $this->assertDatabaseHas(Category::class, $data);
        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals($data['name'], $category->name);
        $this->assertEquals($data['slug'], $category->slug);
    }

    public function test_it_has_a_profile_relationship(): void
    {
        $category     = Category::factory()->create();
        $profile      = CategoryProfile::factory()->create(['category_id' => $category->id]);

        $this->assertTrue($category->profile()->exists());
        $this->assertInstanceOf(CategoryProfile::class, $category->profile);
        $this->assertEquals($profile->id, $category->profile->id);
    }

    public function test_it_can_have_many_articles(): void
    {
        $category      = Category::factory()->create();
        $articles      = Article::factory()->count(3)->create();

        $category->articles()->attach($articles->pluck('id'));

        $this->assertCount(3, $category->articles);
        $this->assertInstanceOf(Article::class, $category->articles->first());
    }

    public function test_it_logs_activity_on_create(): void
    {
        Category::factory()->create([
            'name' => 'PHP',
            'slug' => 'php',
        ]);

        $activity = Activity::latest()->first();

        $this->assertSame('category', $activity->log_name);
        $this->assertSame('created', $activity->description);
        $this->assertArrayHasKey('name', $activity->properties['attributes']);
        $this->assertArrayHasKey('slug', $activity->properties['attributes']);
        $this->assertSame('PHP', $activity->properties['attributes']['name']);
        $this->assertSame('php', $activity->properties['attributes']['slug']);
    }

    public function test_it_logs_only_dirty_attributes_on_update(): void
    {
        $category = Category::factory()->create([
            'name' => 'Old',
            'slug' => 'old',
        ]);

        $category->update(['name' => 'New']);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('updated', $activity->description);
        $this->assertArrayHasKey('name', $activity->properties['attributes']);
        $this->assertArrayNotHasKey('slug', $activity->properties['attributes']);
        $this->assertSame('New', $activity->properties['attributes']['name']);
        $this->assertSame('Old', $activity->properties['old']['name']);
    }

    public function test_it_does_not_log_when_nothing_changes(): void
    {
        $category = Category::factory()->create();

        $initialCount = Activity::count();

        $category->update(['name' => $category->name]);

        $this->assertSame($initialCount, Activity::count());
    }
}
