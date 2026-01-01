<?php

namespace Modules\Course\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\Course\Models\CourseLesson;
use Modules\Course\Observers\CourseLessonObserver;
use Modules\Course\Tests\TestCase;

class CourseLessonObserverTest extends TestCase
{
    private CourseLessonObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->observer = new CourseLessonObserver();
    }

    public function test_saved_clears_cache(): void
    {
        $courseLesson = CourseLesson::factory()->make([
            'id'      => 10,
            'slug'    => 'lesson',
            'chapter' => (object) ['course_id' => 1],
        ]);

        Cache::shouldReceive('forget')->once()->with('course:1');
        Cache::shouldReceive('forget')->once()->with('course:1:cirriculum');
        Cache::shouldReceive('forget')->once()->with('course:1:lesson:10');
        Cache::shouldReceive('forget')->once()->with('course:1:lesson:lesson:id');

        $this->observer->saved($courseLesson);
    }

    public function test_deleted_clears_cache(): void
    {
        $courseLesson = CourseLesson::factory()->make([
            'id'      => 10,
            'slug'    => 'lesson',
            'chapter' => (object) ['course_id' => 1],
        ]);

        Cache::shouldReceive('forget')->once()->with('course:1');
        Cache::shouldReceive('forget')->once()->with('course:1:cirriculum');
        Cache::shouldReceive('forget')->once()->with('course:1:lesson:10');
        Cache::shouldReceive('forget')->once()->with('course:1:lesson:lesson:id');

        $this->observer->deleted($courseLesson);
    }
}
