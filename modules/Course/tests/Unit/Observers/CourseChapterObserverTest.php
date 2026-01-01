<?php

namespace Modules\Course\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseChapter;
use Modules\Course\Observers\CourseChapterObserver;
use Modules\Course\Tests\TestCase;

class CourseChapterObserverTest extends TestCase
{
    private CourseChapterObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->observer = new CourseChapterObserver();
    }

    public function test_saved_clears_cache(): void
    {
        $course        = Course::factory()->create(['id' => 1]);
        $courseChapter = CourseChapter::factory()->create([
            'id'        => 2,
            'course_id' => $course->id,
        ]);

        Cache::shouldReceive('forget')->once()->with("course:1");
        Cache::shouldReceive('forget')->once()->with("course:1:cirriculum");

        $this->observer->saved($courseChapter);
    }

    public function test_deleted_clears_cache(): void
    {
        $course        = Course::factory()->create(['id' => 1]);
        $courseChapter = CourseChapter::factory()->create([
            'id'        => 2,
            'course_id' => $course->id,
        ]);

        Cache::shouldReceive('forget')->once()->with("course:1");
        Cache::shouldReceive('forget')->once()->with("course:1:cirriculum");

        $this->observer->deleted($courseChapter);
    }
}
