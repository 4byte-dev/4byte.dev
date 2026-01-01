<?php

namespace Modules\Course\Tests\Unit\Console\Commands;

use Illuminate\Support\Facades\Event;
use Modules\Course\Events\LessonPublishedEvent;
use Modules\Course\Models\CourseLesson;
use Modules\Course\Tests\TestCase;

class ScheduleLessonCommandTest extends TestCase
{
    public function test_it_publishes_pending_lessons(): void
    {
        Event::fake();

        $lessonToPublish = CourseLesson::factory()->create([
            'status'       => 'PENDING',
            'published_at' => now()->subMinute(),
        ]);

        $lessonFuture = CourseLesson::factory()->create([
            'status'       => 'PENDING',
            'published_at' => now()->addDay(),
        ]);

        $lessonPublished = CourseLesson::factory()->create([
            'status'       => 'PUBLISHED',
            'published_at' => now()->subMinute(),
        ]);

        $this->artisan('lesson:schedule')
            ->expectsOutput('Pending lessons checked')
            ->assertExitCode(0);

        $this->assertEquals('PUBLISHED', $lessonToPublish->refresh()->status);
        $this->assertEquals('PENDING', $lessonFuture->refresh()->status);
        $this->assertEquals('PUBLISHED', $lessonPublished->refresh()->status);

        Event::assertDispatched(LessonPublishedEvent::class, function ($event) use ($lessonToPublish) {
            return $event->lesson->id === $lessonToPublish->id;
        });

        Event::assertDispatchedTimes(LessonPublishedEvent::class, 1);

        Event::assertNotDispatched(LessonPublishedEvent::class, function ($event) use ($lessonFuture) {
            return $event->lesson->id === $lessonFuture->id;
        });

        $this->assertEquals('PENDING', $lessonFuture->refresh()->status);
    }

    public function test_it_does_not_publish_lessons_without_publish_date(): void
    {
        Event::fake();

        $lesson = CourseLesson::factory()->create([
            'status'       => 'PENDING',
            'published_at' => null,
        ]);

        $this->artisan('lesson:schedule');

        $this->assertEquals('PENDING', $lesson->refresh()->status);
        Event::assertNotDispatched(LessonPublishedEvent::class);
    }

    public function test_it_publishes_multiple_pending_lessons(): void
    {
        Event::fake();

        $lessons = CourseLesson::factory()->count(3)->create([
            'status'       => 'PENDING',
            'published_at' => now()->subMinute(),
        ]);

        $this->artisan('lesson:schedule');

        foreach ($lessons as $lesson) {
            $this->assertEquals('PUBLISHED', $lesson->refresh()->status);
        }

        Event::assertDispatchedTimes(LessonPublishedEvent::class, 3);
    }

    public function test_command_is_idempotent(): void
    {
        Event::fake();

        $lesson = CourseLesson::factory()->create([
            'status'       => 'PENDING',
            'published_at' => now()->subMinute(),
        ]);

        $this->artisan('lesson:schedule');
        $this->artisan('lesson:schedule');

        Event::assertDispatchedTimes(LessonPublishedEvent::class, 1);
        $this->assertEquals('PUBLISHED', $lesson->refresh()->status);
    }
}
