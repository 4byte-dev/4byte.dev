<?php

namespace Modules\Page\Tests\Unit\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Modules\Page\Models\Page;
use Modules\Page\Notifications\PagePublishedNotification;
use Modules\Page\Tests\TestCase;

class PagePublishedNotificationTest extends TestCase
{
    public function test_notification_content(): void
    {
        $page = Page::factory()->make([
            'title'   => 'Test Page',
            'slug'    => 'test-page',
            'excerpt' => 'Test Page Excerpt',
        ]);

        $notification = new PagePublishedNotification($page);

        $this->assertEquals(['mail'], $notification->via());

        $mailData = $notification->toMail();
        $this->assertInstanceOf(MailMessage::class, $mailData);

        $arrayData = $notification->toArray();
        $this->assertEquals('Test Page', $arrayData['title']);
        $this->assertStringContainsString('test-page', $arrayData['url']);
    }
}
