<?php

namespace Modules\Course\Tests\Unit\Listeners;

use Illuminate\Support\Facades\Notification;
use Modules\Course\Events\CoursePublishedEvent;
use Modules\Course\Listeners\CoursePublishedListener;
use Modules\Course\Models\Course;
use Modules\Course\Notifications\CoursePublishedNotification;
use Modules\Course\Tests\TestCase;
use Modules\User\Models\User;

class CoursePublishedListenerTest extends TestCase
{
    public function test_it_sends_notification(): void
    {
        Notification::fake();

        $user     = User::factory()->create();
        $course   = Course::factory()->create(['user_id' => $user->id]);
        $event    = new CoursePublishedEvent($course);
        $listener = new CoursePublishedListener();

        $listener->handle($event);

        Notification::assertSentTo(
            [$user],
            CoursePublishedNotification::class,
            function ($notification, $channels) use ($course) {
                return $notification->course->id === $course->id;
            }
        );
    }
}
