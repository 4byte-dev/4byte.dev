<?php

namespace Modules\Course\Tests\Unit\Models;

use Modules\Category\Models\Category;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseChapter;
use Modules\Course\Tests\TestCase;
use Modules\Tag\Models\Tag;
use Modules\User\Models\User;
use Spatie\Activitylog\Models\Activity;

class CourseTest extends TestCase
{
    public function test_fillable_attributes_are_correct(): void
    {
        $course = new Course();

        $this->assertEquals(
            [
                'title',
                'slug',
                'difficulty',
                'excerpt',
                'content',
                'status',
                'published_at',
                'user_id',
            ],
            $course->getFillable()
        );
    }

    public function test_casts_are_correct(): void
    {
        $course = new Course();

        $this->assertEquals('datetime', $course->getCasts()['published_at']);
    }

    public function test_it_belongs_to_user(): void
    {
        $user   = User::factory()->create();
        $course = Course::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $course->user);
        $this->assertEquals($user->id, $course->user->id);
    }

    public function test_it_belongs_to_many_categories(): void
    {
        $course   = Course::factory()->create();
        $category = Category::factory()->create();

        $course->categories()->attach($category);

        $this->assertTrue($course->categories->contains($category));
    }

    public function test_it_belongs_to_many_tags(): void
    {
        $course = Course::factory()->create();
        $tag    = Tag::factory()->create();

        $course->tags()->attach($tag);

        $this->assertTrue($course->tags->contains($tag));
    }

    public function test_it_has_many_chapters(): void
    {
        $course = Course::factory()->create();

        $chapter = CourseChapter::factory()->create([
            'course_id' => $course->id,
        ]);

        $this->assertTrue($course->chapters->contains($chapter));
    }

    public function test_it_logs_activity_on_create(): void
    {
        Course::factory()->create([
            'title' => 'New Course',
        ]);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('course', $activity->log_name);
        $this->assertSame('created', $activity->description);
        $this->assertSame('New Course', $activity->properties['attributes']['title']);
    }

    public function test_it_logs_only_dirty_attributes_on_update(): void
    {
        $course = Course::factory()->create(['title' => 'Old Title']);

        $course->update(['title' => 'New Title']);

        $activity = Activity::orderBy('id', 'desc')->first();

        $this->assertSame('updated', $activity->description);
        $this->assertSame('New Title', $activity->properties['attributes']['title']);
        $this->assertSame('Old Title', $activity->properties['old']['title']);
    }

    public function test_it_does_not_log_when_nothing_changes(): void
    {
        $course = Course::factory()->create();

        $initialCount = Activity::count();

        $course->update(['title' => $course->title]);

        $this->assertSame($initialCount, Activity::count());
    }

    public function test_it_is_searchable_only_when_published(): void
    {
        $draftCourse     = Course::factory()->create(['status' => 'DRAFT']);
        $publishedCourse = Course::factory()->create(['status' => 'PUBLISHED']);

        $this->assertFalse($draftCourse->shouldBeSearchable());
        $this->assertTrue($publishedCourse->shouldBeSearchable());
    }

    public function test_searchable_array_structure(): void
    {
        $course = Course::factory()->create();

        $array = $course->toSearchableArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertEquals($course->title, $array['title']);
    }
}
