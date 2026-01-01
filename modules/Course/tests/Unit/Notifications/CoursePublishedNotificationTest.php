<?php

namespace Modules\Course\Tests\Unit\Notifications;

use Modules\Course\Models\Course;
use Modules\Course\Notifications\CoursePublishedNotification;
use Modules\Course\Tests\TestCase;

class CoursePublishedNotificationTest extends TestCase
{
    public function test_notification_content(): void
    {
        $course = Course::factory()->make([
            'title'   => 'Test Course',
            'slug'    => 'test-course',
        ]);

        $notification = new CoursePublishedNotification($course);

        $this->assertEquals(['mail'], $notification->via());

        $mailData = $notification->toMail();

        $this->assertInstanceOf(\Illuminate\Notifications\Messages\MailMessage::class, $mailData);

        $arrayData = $notification->toArray();
        $this->assertEquals('Test Course', $arrayData['title']);
        $this->assertStringContainsString('test-course', $arrayData['url']);
    }
}
