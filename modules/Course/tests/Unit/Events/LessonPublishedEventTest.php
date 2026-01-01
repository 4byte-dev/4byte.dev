<?php

namespace Modules\Course\Tests\Unit\Events;

use Illuminate\Support\Facades\Event;
use Modules\Course\Events\LessonPublishedEvent;
use Modules\Course\Models\CourseLesson;
use Modules\Course\Tests\TestCase;

class LessonPublishedEventTest extends TestCase
{
    public function test_it_has_lesson(): void
    {
        $lesson = CourseLesson::factory()->create();
        $event  = new LessonPublishedEvent($lesson);

        $this->assertSame($lesson, $event->lesson);
    }

    public function test_event_dispatch(): void
    {
        Event::fake();

        $lesson = CourseLesson::factory()->create();
        LessonPublishedEvent::dispatch($lesson);

        Event::assertDispatched(LessonPublishedEvent::class, function ($event) use ($lesson) {
            return $event->lesson->id === $lesson->id;
        });
    }
}
