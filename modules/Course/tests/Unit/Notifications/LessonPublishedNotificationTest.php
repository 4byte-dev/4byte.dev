<?php

namespace Modules\Course\Tests\Unit\Notifications;

use Modules\Course\Models\CourseLesson;
use Modules\Course\Notifications\LessonPublishedNotification;
use Modules\Course\Tests\TestCase;

class LessonPublishedNotificationTest extends TestCase
{
    public function test_notification_content(): void
    {
        $course = CourseLesson::factory()->make([
            'title'   => 'Test Lesson',
            'slug'    => 'test-lesson',
        ]);

        $notification = new LessonPublishedNotification($course);

        $this->assertEquals(['mail'], $notification->via());

        $mailData = $notification->toMail();

        $this->assertInstanceOf(\Illuminate\Notifications\Messages\MailMessage::class, $mailData);

        $arrayData = $notification->toArray();
        $this->assertEquals('Test Lesson', $arrayData['title']);
        $this->assertStringContainsString('test-lesson', $arrayData['url']);
    }
}
