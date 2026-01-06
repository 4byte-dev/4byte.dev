<?php

namespace Modules\Page\Tests\Unit\Models;

use App\Models\User;
use Modules\Page\Models\Page;
use Modules\Page\Tests\TestCase;
use Spatie\Activitylog\Models\Activity;

class PageTest extends TestCase
{
    public function test_fillable_attributes_are_correct(): void
    {
        $page = new Page();

        $this->assertEquals(
            [
                'title',
                'slug',
                'excerpt',
                'content',
                'status',
                'published_at',
                'user_id',
            ],
            $page->getFillable()
        );
    }

    public function test_casts_are_correct(): void
    {
        $page = new Page();

        $this->assertEquals('datetime', $page->getCasts()['published_at']);
    }

    public function test_it_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $page = Page::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $page->user);
        $this->assertEquals($user->id, $page->user->id);
    }

    public function test_it_logs_activity_on_create(): void
    {
        Page::factory()->create([
            'title' => 'New Page',
        ]);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('page', $activity->log_name);
        $this->assertSame('created', $activity->description);
        $this->assertSame('New Page', $activity->properties['attributes']['title']);
    }

    public function test_it_logs_only_dirty_attributes_on_update(): void
    {
        $page = Page::factory()->create(['title' => 'Old Title']);

        $page->update(['title' => 'New Title']);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('updated', $activity->description);
        $this->assertSame('New Title', $activity->properties['attributes']['title']);
        $this->assertSame('Old Title', $activity->properties['old']['title']);
    }

    public function test_it_does_not_log_when_nothing_changes(): void
    {
        $page = Page::factory()->create();

        $initialCount = Activity::count();

        $page->update(['title' => $page->title]);

        $this->assertSame($initialCount, Activity::count());
    }

    public function test_it_is_searchable_only_when_published(): void
    {
        $draftPage     = Page::factory()->create(['status' => 'PENDING']);
        $publishedPage = Page::factory()->create(['status' => 'PUBLISHED']);

        $this->assertFalse($draftPage->shouldBeSearchable());
        $this->assertTrue($publishedPage->shouldBeSearchable());
    }

    public function test_searchable_array_structure(): void
    {
        $page = Page::factory()->create();

        $array = $page->toSearchableArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertEquals($page->title, $array['title']);
    }

    public function test_media_collections(): void
    {
        $page = new Page();
        $page->registerMediaCollections();

        $this->assertCount(2, $page->mediaCollections);
        $this->assertEquals(
            ['content', 'cover'],
            array_keys($page->mediaCollections)
        );
    }
}
