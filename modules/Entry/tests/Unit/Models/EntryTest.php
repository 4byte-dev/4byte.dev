<?php

namespace Modules\Entry\Tests\Unit\Models;

use App\Models\User;
use Modules\Entry\Models\Entry;
use Modules\Entry\Tests\TestCase;
use Spatie\Activitylog\Models\Activity;

class EntryTest extends TestCase
{
    public function test_fillable_attributes_are_correct(): void
    {
        $entry = new Entry();

        $this->assertEquals(
            [
                'slug',
                'content',
                'user_id',
            ],
            $entry->getFillable()
        );
    }

    public function test_it_belongs_to_user(): void
    {
        $user  = User::factory()->create();
        $entry = Entry::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $entry->user);
        $this->assertEquals($user->id, $entry->user->id);
    }

    public function test_it_logs_activity_on_create(): void
    {
        Entry::factory()->create([
            'content' => 'Initial content',
        ]);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('entry', $activity->log_name);
        $this->assertSame('created', $activity->description);
        $this->assertSame(
            'Initial content',
            $activity->properties['attributes']['content']
        );
    }

    public function test_it_logs_only_dirty_attributes_on_update(): void
    {
        $entry = Entry::factory()->create([
            'content' => 'Old content',
        ]);

        $entry->update([
            'content' => 'New content',
        ]);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('updated', $activity->description);
        $this->assertSame(
            'New content',
            $activity->properties['attributes']['content']
        );
        $this->assertSame(
            'Old content',
            $activity->properties['old']['content']
        );
    }

    public function test_it_does_not_log_when_nothing_changes(): void
    {
        $entry = Entry::factory()->create();

        $initialCount = Activity::count();

        $entry->update([
            'content' => $entry->content,
        ]);

        $this->assertSame($initialCount, Activity::count());
    }

    public function test_get_content_images_returns_empty_array_when_no_media(): void
    {
        $entry = Entry::factory()->create();

        $images = $entry->getContentImages();

        $this->assertEmpty($images);
    }
}
