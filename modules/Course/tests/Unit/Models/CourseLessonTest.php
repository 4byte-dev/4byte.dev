<?php

namespace Modules\Course\Tests\Unit\Models;

use Modules\Course\Models\CourseChapter;
use Modules\Course\Models\CourseLesson;
use Modules\Course\Tests\TestCase;
use Modules\User\Models\User;
use Spatie\Activitylog\Models\Activity;

class CourseLessonTest extends TestCase
{
    public function test_fillable_attributes_are_correct(): void
    {
        $lesson = new CourseLesson();

        $this->assertEquals(
            [
                'title',
                'slug',
                'content',
                'video_url',
                'status',
                'published_at',
                'user_id',
                'chapter_id',
            ],
            $lesson->getFillable()
        );
    }

    public function test_casts_are_correct(): void
    {
        $lesson = new CourseLesson();

        $this->assertEquals('datetime', $lesson->getCasts()['published_at']);
    }

    public function test_it_belongs_to_user(): void
    {
        $user    = User::factory()->create();
        $lesson  = CourseLesson::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $lesson->user);
        $this->assertEquals($user->id, $lesson->user->id);
    }

    public function test_it_belongs_to_chapter(): void
    {
        $chapter = CourseChapter::factory()->create();
        $lesson  = CourseLesson::factory()->create([
            'chapter_id' => $chapter->id,
        ]);

        $this->assertInstanceOf(CourseChapter::class, $lesson->chapter);
        $this->assertEquals($chapter->id, $lesson->chapter->id);
    }

    public function test_it_logs_activity_on_create(): void
    {
        CourseLesson::factory()->create([
            'title' => 'Lesson 1',
        ]);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('course', $activity->log_name);
        $this->assertSame('created', $activity->description);
        $this->assertSame(
            'Lesson 1',
            $activity->properties['attributes']['title']
        );
    }

    public function test_it_logs_only_dirty_attributes_on_update(): void
    {
        $lesson = CourseLesson::factory()->create([
            'title' => 'Old Lesson',
        ]);

        $lesson->update(['title' => 'New Lesson']);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('updated', $activity->description);
        $this->assertSame(
            'New Lesson',
            $activity->properties['attributes']['title']
        );
        $this->assertSame(
            'Old Lesson',
            $activity->properties['old']['title']
        );
    }

    public function test_it_does_not_log_when_nothing_changes(): void
    {
        $lesson = CourseLesson::factory()->create();

        $initialCount = Activity::count();

        $lesson->update([
            'title' => $lesson->title,
        ]);

        $this->assertSame($initialCount, Activity::count());
    }

    public function test_it_is_searchable_only_when_published(): void
    {
        $draftLesson     = CourseLesson::factory()->create(['status' => 'DRAFT']);
        $publishedLesson = CourseLesson::factory()->create(['status' => 'PUBLISHED']);

        $this->assertFalse($draftLesson->shouldBeSearchable());
        $this->assertTrue($publishedLesson->shouldBeSearchable());
    }

    public function test_searchable_array_structure(): void
    {
        $lesson = CourseLesson::factory()->create();

        $array = $lesson->toSearchableArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('title', $array);

        $this->assertEquals($lesson->id, $array['id']);
        $this->assertEquals($lesson->title, $array['title']);
    }
}
