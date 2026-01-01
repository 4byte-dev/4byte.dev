<?php

namespace Modules\Course\Tests\Unit\Events;

use Illuminate\Support\Facades\Event;
use Modules\Course\Events\CoursePublishedEvent;
use Modules\Course\Models\Course;
use Modules\Course\Tests\TestCase;

class CoursePublishedEventTest extends TestCase
{
    public function test_it_has_course(): void
    {
        $course = Course::factory()->create();
        $event  = new CoursePublishedEvent($course);

        $this->assertSame($course, $event->course);
    }

    public function test_event_dispatch(): void
    {
        Event::fake();

        $course = Course::factory()->create();
        CoursePublishedEvent::dispatch($course);

        Event::assertDispatched(CoursePublishedEvent::class, function ($event) use ($course) {
            return $event->course->id === $course->id;
        });
    }
}
