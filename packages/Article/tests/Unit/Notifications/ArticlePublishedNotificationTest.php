<?php

namespace Packages\Article\Tests\Unit\Notifications;

use Packages\Article\Models\Article;
use Packages\Article\Notifications\ArticlePublishedNotification;
use Packages\Article\Tests\TestCase;

class ArticlePublishedNotificationTest extends TestCase
{
    public function test_notification_content(): void
    {
        $article = Article::factory()->make([
            'title'   => 'Test Article',
            'slug'    => 'test-article',
            'excerpt' => 'Test Excerpt',
        ]);

        $notification = new ArticlePublishedNotification($article);

        $this->assertEquals(['mail'], $notification->via());

        $mailData = $notification->toMail();

        $this->assertInstanceOf(\Illuminate\Notifications\Messages\MailMessage::class, $mailData);

        $arrayData = $notification->toArray();
        $this->assertEquals('Test Article', $arrayData['title']);
        $this->assertStringContainsString('test-article', $arrayData['url']);
    }
}
