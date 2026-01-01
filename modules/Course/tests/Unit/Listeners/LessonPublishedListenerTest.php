<?php

namespace Modules\Course\Tests\Unit\Listeners;

use Illuminate\Support\Facades\Notification;
use Modules\Course\Events\LessonPublishedEvent;
use Modules\Course\Listeners\LessonPublishedListener;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseChapter;
use Modules\Course\Models\CourseLesson;
use Modules\Course\Notifications\LessonPublishedNotification;
use Modules\Course\Tests\TestCase;
use Modules\User\Models\User;

class LessonPublishedListenerTest extends TestCase
{
    public function test_it_sends_notification(): void
    {
        Notification::fake();

        $user    = User::factory()->create();
        $course  = Course::factory()->create(['user_id' => $user->id]);
        $chapter = CourseChapter::factory()->create(['course_id' => $course->id]);
        $lesson  = CourseLesson::factory()->create(['chapter_id' => $chapter->id]);

        $event    = new LessonPublishedEvent($lesson);
        $listener = new LessonPublishedListener();

        $listener->handle($event);

        Notification::assertSentTo(
            [$course->user],
            LessonPublishedNotification::class,
            function ($notification, $channels) use ($lesson) {
                return $notification->lesson->id === $lesson->id;
            }
        );
    }
}
