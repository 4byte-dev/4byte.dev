<?php

namespace Packages\Category\Tests\Unit\Models;

use Packages\Category\Models\Category;
use Packages\Category\Models\CategoryProfile;
use Packages\Category\Tests\TestCase;
use Spatie\Activitylog\Models\Activity;

class CategoryProfileTest extends TestCase
{
    public function test_fillable_attributes_are_correct(): void
    {
        $profile = new CategoryProfile();

        $this->assertSame(
            ['description', 'color', 'category_id'],
            $profile->getFillable()
        );
    }

    public function test_it_can_be_created_via_factory(): void
    {
        $profile = CategoryProfile::factory()->create();

        $this->assertInstanceOf(CategoryProfile::class, $profile);
        $this->assertNotEmpty($profile->description);
        $this->assertNotEmpty($profile->color);
    }

    public function test_it_can_be_mass_assigned(): void
    {
        $category = Category::factory()->create();

        $data = [
            'description'      => 'test-description',
            'color'            => '#ffffff',
            'category_id'      => $category->id,
        ];

        $profile = CategoryProfile::create($data);

        $this->assertDatabaseHas(CategoryProfile::class, $data);
        $this->assertInstanceOf(CategoryProfile::class, $profile);
        $this->assertEquals($data['description'], $profile->description);
        $this->assertEquals($data['color'], $profile->color);
    }

    public function test_it_belongs_to_a_category(): void
    {
        $category     = Category::factory()->create();
        $profile      = CategoryProfile::create([
            'description'   => 'test-description',
            'color'         => '#ffffff',
            'category_id'   => $category->id,
        ]);

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            $profile->category()
        );

        $this->assertTrue($profile->category->is($category));
    }

    public function test_it_logs_activity_on_create(): void
    {
        $category = Category::factory()->create();
        CategoryProfile::create([
            'description'      => 'test-description',
            'color'            => '#ffffff',
            'category_id'      => $category->id,
        ]);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('category_profile', $activity->log_name);
        $this->assertSame('created', $activity->description);
        $this->assertArrayHasKey('description', $activity->properties['attributes']);
        $this->assertArrayHasKey('color', $activity->properties['attributes']);
        $this->assertSame('test-description', $activity->properties['attributes']['description']);
        $this->assertSame('#ffffff', $activity->properties['attributes']['color']);
    }

    public function test_it_logs_only_dirty_attributes_on_update(): void
    {
        $category     = Category::factory()->create();
        $profile      = CategoryProfile::create([
            'description'      => 'Old',
            'color'            => '#ffffff',
            'category_id'      => $category->id,
        ]);

        $profile->update([
            'description' => 'New',
        ]);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('category_profile', $activity->log_name);
        $this->assertSame('updated', $activity->description);
        $this->assertArrayHasKey('description', $activity->properties['attributes']);
        $this->assertSame('New', $activity->properties['attributes']['description']);
        $this->assertSame('Old', $activity->properties['old']['description']);
    }

    public function test_it_does_not_log_when_nothing_changes(): void
    {
        $category = CategoryProfile::factory()->create();

        $initialCount = Activity::count();

        $category->update(['description' => $category->description]);

        $this->assertSame($initialCount, Activity::count());
    }
}
