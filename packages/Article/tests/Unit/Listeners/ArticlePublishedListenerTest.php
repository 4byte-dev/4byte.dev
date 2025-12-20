<?php

namespace Packages\Article\Tests\Unit\Listeners;

use Illuminate\Support\Facades\Notification as NotificationFacade;
use Packages\Article\Events\ArticlePublishedEvent;
use Packages\Article\Listeners\ArticlePublishedListener;
use Packages\Article\Models\Article;
use Packages\Article\Notifications\ArticlePublishedNotification;
use Packages\Article\Tests\TestCase;

class ArticlePublishedListenerTest extends TestCase
{
    public function test_listener_sends_notifications(): void
    {
        NotificationFacade::fake();

        $article  = Article::factory()->create();
        $event    = new ArticlePublishedEvent($article);
        $listener = new ArticlePublishedListener();

        $listener->handle($event);

        NotificationFacade::assertSentTo(
            $article->user,
            ArticlePublishedNotification::class,
            function ($notification, $channels) use ($article) {
                return $notification->article->id === $article->id;
            }
        );
    }
}
