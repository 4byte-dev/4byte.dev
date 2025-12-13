<?php

namespace Packages\Tag\Tests\Unit\Models;

use Packages\Article\Models\Article;
use Packages\Tag\Models\Tag;
use Packages\Tag\Models\TagProfile;
use Packages\Tag\Tests\TestCase;
use Spatie\Activitylog\Models\Activity;

class TagTest extends TestCase
{
    public function test_fillable_attributes_are_correct(): void
    {
        $tag = new Tag();

        $this->assertSame(
            ['name', 'slug'],
            $tag->getFillable()
        );
    }

    public function test_it_can_be_created_via_factory(): void
    {
        $tag = Tag::factory()->create();

        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertNotEmpty($tag->name);
        $this->assertNotEmpty($tag->slug);
    }

    public function test_it_can_be_mass_assigned(): void
    {
        $data = [
            'name' => 'Laravel',
            'slug' => 'laravel',
        ];

        $tag = Tag::create($data);

        $this->assertDatabaseHas(Tag::class, $data);
        $this->assertInstanceOf(Tag::class, $tag);
        $this->assertEquals($data['name'], $tag->name);
        $this->assertEquals($data['slug'], $tag->slug);
    }

    public function test_it_has_a_profile_relationship(): void
    {
        $tag     = Tag::factory()->create();
        $profile = TagProfile::factory()->create(['tag_id' => $tag->id]);

        $this->assertTrue($tag->profile()->exists());
        $this->assertInstanceOf(TagProfile::class, $tag->profile);
        $this->assertEquals($profile->id, $tag->profile->id);
    }

    public function test_it_can_have_many_articles(): void
    {
        $tag      = Tag::factory()->create();
        $articles = Article::factory()->count(3)->create();

        $tag->articles()->attach($articles->pluck('id'));

        $this->assertCount(3, $tag->articles);
        $this->assertInstanceOf(Article::class, $tag->articles->first());
    }

    public function test_it_logs_activity_on_create(): void
    {
        Tag::factory()->create([
            'name' => 'PHP',
            'slug' => 'php',
        ]);

        $activity = Activity::latest()->first();

        $this->assertSame('tag', $activity->log_name);
        $this->assertSame('created', $activity->description);
        $this->assertArrayHasKey('name', $activity->properties['attributes']);
        $this->assertArrayHasKey('slug', $activity->properties['attributes']);
        $this->assertSame('PHP', $activity->properties['attributes']['name']);
        $this->assertSame('php', $activity->properties['attributes']['slug']);
    }

    public function test_it_logs_only_dirty_attributes_on_update(): void
    {
        $tag = Tag::factory()->create([
            'name' => 'Old',
            'slug' => 'old',
        ]);

        $tag->update(['name' => 'New']);

        $activity = Activity::find(2);

        $this->assertSame('updated', $activity->description);
        $this->assertArrayHasKey('name', $activity->properties['attributes']);
        $this->assertArrayNotHasKey('slug', $activity->properties['attributes']);
        $this->assertSame('New', $activity->properties['attributes']['name']);
        $this->assertSame('Old', $activity->properties['old']['name']);
    }

    public function test_it_does_not_log_when_nothing_changes(): void
    {
        $tag = Tag::factory()->create();

        $initialCount = Activity::count();

        $tag->update(['name' => $tag->name]);

        $this->assertSame($initialCount, Activity::count());
    }
}
