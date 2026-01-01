<?php

namespace Modules\Course\Tests\Unit\Console\Commands;

use Illuminate\Support\Facades\Event;
use Modules\Course\Events\CoursePublishedEvent;
use Modules\Course\Models\Course;
use Modules\Course\Tests\TestCase;

class ScheduleCourseCommandTest extends TestCase
{
    public function test_it_publishes_pending_courses(): void
    {
        Event::fake();

        $courseToPublish = Course::factory()->create([
            'status'       => 'PENDING',
            'published_at' => now()->subMinute(),
        ]);

        $courseFuture = Course::factory()->create([
            'status'       => 'PENDING',
            'published_at' => now()->addDay(),
        ]);

        $coursePublished = Course::factory()->create([
            'status'       => 'PUBLISHED',
            'published_at' => now()->subMinute(),
        ]);

        $this->artisan('course:schedule')
            ->expectsOutput('Pending courses checked')
            ->assertExitCode(0);

        $this->assertEquals('PUBLISHED', $courseToPublish->refresh()->status);
        $this->assertEquals('PENDING', $courseFuture->refresh()->status);
        $this->assertEquals('PUBLISHED', $coursePublished->refresh()->status);

        Event::assertDispatched(CoursePublishedEvent::class, function ($event) use ($courseToPublish) {
            return $event->course->id === $courseToPublish->id;
        });

        Event::assertDispatchedTimes(CoursePublishedEvent::class, 1);

        Event::assertNotDispatched(CoursePublishedEvent::class, function ($event) use ($courseFuture) {
            return $event->course->id === $courseFuture->id;
        });

        $this->assertEquals('PENDING', $courseFuture->refresh()->status);
    }

    public function test_it_does_not_publish_courses_without_publish_date(): void
    {
        Event::fake();

        $course = Course::factory()->create([
            'status'       => 'PENDING',
            'published_at' => null,
        ]);

        $this->artisan('course:schedule');

        $this->assertEquals('PENDING', $course->refresh()->status);
        Event::assertNotDispatched(CoursePublishedEvent::class);
    }

    public function test_it_publishes_multiple_pending_courses(): void
    {
        Event::fake();

        $courses = Course::factory()->count(3)->create([
            'status'       => 'PENDING',
            'published_at' => now()->subMinute(),
        ]);

        $this->artisan('course:schedule');

        foreach ($courses as $course) {
            $this->assertEquals('PUBLISHED', $course->refresh()->status);
        }

        Event::assertDispatchedTimes(CoursePublishedEvent::class, 3);
    }

    public function test_command_is_idempotent(): void
    {
        Event::fake();

        $course = Course::factory()->create([
            'status'       => 'PENDING',
            'published_at' => now()->subMinute(),
        ]);

        $this->artisan('course:schedule');
        $this->artisan('course:schedule');

        Event::assertDispatchedTimes(CoursePublishedEvent::class, 1);
        $this->assertEquals('PUBLISHED', $course->refresh()->status);
    }
}
