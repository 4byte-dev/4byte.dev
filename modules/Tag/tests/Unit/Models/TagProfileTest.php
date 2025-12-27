<?php

namespace Modules\Tag\Tests\Unit\Models;

use Modules\Category\Models\Category;
use Modules\Tag\Models\Tag;
use Modules\Tag\Models\TagProfile;
use Modules\Tag\Tests\TestCase;
use Spatie\Activitylog\Models\Activity;

class TagProfileTest extends TestCase
{
    public function test_fillable_attributes_are_correct(): void
    {
        $profile = new TagProfile();

        $this->assertSame(
            ['description', 'color', 'tag_id'],
            $profile->getFillable()
        );
    }

    public function test_it_can_be_created_via_factory(): void
    {
        $profile = TagProfile::factory()->create();

        $this->assertInstanceOf(TagProfile::class, $profile);
        $this->assertNotEmpty($profile->description);
        $this->assertNotEmpty($profile->color);
    }

    public function test_it_can_be_mass_assigned(): void
    {
        $tag = Tag::factory()->create();

        $data = [
            'description' => 'test-description',
            'color'       => '#ffffff',
            'tag_id'      => $tag->id,
        ];

        $profile = TagProfile::create($data);

        $this->assertDatabaseHas(TagProfile::class, $data);
        $this->assertInstanceOf(TagProfile::class, $profile);
        $this->assertEquals($data['description'], $profile->description);
        $this->assertEquals($data['color'], $profile->color);
    }

    public function test_it_belongs_to_a_tag(): void
    {
        $tag     = Tag::factory()->create();
        $profile = TagProfile::create([
            'description' => 'test-description',
            'color'       => '#ffffff',
            'tag_id'      => $tag->id,
        ]);

        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            $profile->tag()
        );

        $this->assertTrue($profile->tag->is($tag));
    }

    public function test_it_can_have_many_categories(): void
    {
        $tag     = Tag::factory()->create();
        $profile = TagProfile::create([
            'description' => 'test-description',
            'color'       => '#ffffff',
            'tag_id'      => $tag->id,
        ]);
        $categories = Category::factory()->count(3)->create();

        $profile->categories()->attach($categories->pluck('id'));

        $this->assertCount(3, $profile->categories);
        $this->assertInstanceOf(Category::class, $profile->categories->first());
    }

    public function test_it_logs_activity_on_create(): void
    {
        $tag = Tag::factory()->create();
        TagProfile::create([
            'description' => 'test-description',
            'color'       => '#ffffff',
            'tag_id'      => $tag->id,
        ]);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('tag_profile', $activity->log_name);
        $this->assertSame('created', $activity->description);
        $this->assertArrayHasKey('description', $activity->properties['attributes']);
        $this->assertArrayHasKey('color', $activity->properties['attributes']);
        $this->assertSame('test-description', $activity->properties['attributes']['description']);
        $this->assertSame('#ffffff', $activity->properties['attributes']['color']);
    }

    public function test_it_logs_only_dirty_attributes_on_update(): void
    {
        $tag     = Tag::factory()->create();
        $profile = TagProfile::create([
            'description' => 'Old',
            'color'       => '#ffffff',
            'tag_id'      => $tag->id,
        ]);

        $profile->update([
            'description' => 'New',
        ]);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('tag_profile', $activity->log_name);
        $this->assertSame('updated', $activity->description);
        $this->assertArrayHasKey('description', $activity->properties['attributes']);
        $this->assertSame('New', $activity->properties['attributes']['description']);
        $this->assertSame('Old', $activity->properties['old']['description']);
    }

    public function test_it_does_not_log_when_nothing_changes(): void
    {
        $tag = TagProfile::factory()->create();

        $initialCount = Activity::count();

        $tag->update(['description' => $tag->description]);

        $this->assertSame($initialCount, Activity::count());
    }
}
