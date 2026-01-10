<?php

namespace Modules\Article\Tests\Unit\Jobs;

use Illuminate\Support\Facades\Event;
use Modules\Article\Events\ArticlePublishedEvent;
use Modules\Article\Jobs\PublishArticleJob;
use Modules\Article\Models\Article;
use Modules\Article\Tests\TestCase;

class PublishArticleJobTest extends TestCase
{
    public function test_it_publishes_article(): void
    {
        Event::fake();

        $article = Article::factory()->create([
            'status' => 'PENDING',
        ]);

        $job = new PublishArticleJob($article);
        $job->handle();

        $this->assertEquals('PUBLISHED', $article->refresh()->status);
        Event::assertDispatched(ArticlePublishedEvent::class, function ($event) use ($article) {
            return $event->article->id === $article->id;
        });
    }

    public function test_it_does_not_publish_already_published_article(): void
    {
        Event::fake();

        $article = Article::factory()->create([
            'status' => 'PUBLISHED',
        ]);

        $job = new PublishArticleJob($article);
        $job->handle();

        Event::assertNotDispatched(ArticlePublishedEvent::class);
    }
}
