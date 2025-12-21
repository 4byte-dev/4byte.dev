<?php

namespace Modules\Article\Tests\Unit\Listeners;

use Illuminate\Support\Facades\Notification as NotificationFacade;
use Modules\Article\Events\ArticlePublishedEvent;
use Modules\Article\Listeners\ArticlePublishedListener;
use Modules\Article\Models\Article;
use Modules\Article\Notifications\ArticlePublishedNotification;
use Modules\Article\Tests\TestCase;

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
