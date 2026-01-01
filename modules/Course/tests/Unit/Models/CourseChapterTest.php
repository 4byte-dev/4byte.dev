<?php

namespace Modules\Course\Tests\Unit\Models;

use Modules\Course\Models\Course;
use Modules\Course\Models\CourseChapter;
use Modules\Course\Models\CourseLesson;
use Modules\Course\Tests\TestCase;
use Spatie\Activitylog\Models\Activity;

class CourseChapterTest extends TestCase
{
    public function test_fillable_attributes_are_correct(): void
    {
        $chapter = new CourseChapter();

        $this->assertEquals(
            [
                'title',
                'course_id',
            ],
            $chapter->getFillable()
        );
    }

    public function test_it_belongs_to_course(): void
    {
        $course  = Course::factory()->create();
        $chapter = CourseChapter::factory()->create([
            'course_id' => $course->id,
        ]);

        $this->assertInstanceOf(Course::class, $chapter->course);
        $this->assertEquals($course->id, $chapter->course->id);
    }

    public function test_it_has_many_lessons(): void
    {
        $chapter = CourseChapter::factory()->create();

        $lesson = CourseLesson::factory()->create([
            'chapter_id' => $chapter->id,
        ]);

        $this->assertTrue($chapter->lessons->contains($lesson));
    }

    public function test_it_logs_activity_on_create(): void
    {
        CourseChapter::factory()->create([
            'title' => 'Introduction',
        ]);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('course-chapter', $activity->log_name);
        $this->assertSame('created', $activity->description);
        $this->assertSame(
            'Introduction',
            $activity->properties['attributes']['title']
        );
    }

    public function test_it_logs_only_dirty_attributes_on_update(): void
    {
        $chapter = CourseChapter::factory()->create([
            'title' => 'Old Title',
        ]);

        $chapter->update(['title' => 'New Title']);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('updated', $activity->description);
        $this->assertSame(
            'New Title',
            $activity->properties['attributes']['title']
        );
        $this->assertSame(
            'Old Title',
            $activity->properties['old']['title']
        );
    }

    public function test_it_does_not_log_when_nothing_changes(): void
    {
        $chapter = CourseChapter::factory()->create();

        $initialCount = Activity::count();

        $chapter->update([
            'title' => $chapter->title,
        ]);

        $this->assertSame($initialCount, Activity::count());
    }
}
