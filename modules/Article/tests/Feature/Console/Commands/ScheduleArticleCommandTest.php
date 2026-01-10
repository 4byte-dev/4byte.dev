<?php

namespace Modules\Article\Tests\Feature\Console\Commands;

use Illuminate\Support\Facades\Event;
use Modules\Article\Events\ArticlePublishedEvent;
use Modules\Article\Models\Article;
use Modules\Article\Tests\TestCase;

class ScheduleArticleCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now());
    }

    public function test_it_dispatches_events_for_valid_pending_articles(): void
    {
        Event::fake();

        $articleToPublish = Article::factory()->create([
            'status'       => 'PENDING',
            'published_at' => now()->subMinute(),
        ]);

        $articleFuture = Article::factory()->create([
            'status'       => 'PENDING',
            'published_at' => now()->addDay(),
        ]);

        $articleAlreadyPublished = Article::factory()->create([
            'status'       => 'PUBLISHED',
            'published_at' => now()->subMinute(),
        ]);

        $this->artisan('article:schedule')
            ->expectsOutput('Pending articles checked')
            ->assertExitCode(0);

        Event::assertDispatched(ArticlePublishedEvent::class, function ($event) use ($articleToPublish) {
            return $event->article->id === $articleToPublish->id;
        });

        Event::assertNotDispatched(ArticlePublishedEvent::class, function ($event) use ($articleFuture) {
            return $event->article->id === $articleFuture->id;
        });

        Event::assertNotDispatched(ArticlePublishedEvent::class, function ($event) use ($articleAlreadyPublished) {
            return $event->article->id === $articleAlreadyPublished->id;
        });
    }

    public function test_it_does_not_dispatch_event_without_publish_date(): void
    {
        Event::fake();

        $article = Article::factory()->create([
            'status'       => 'PENDING',
            'published_at' => null,
        ]);

        $this->artisan('article:schedule');

        Event::assertNotDispatched(ArticlePublishedEvent::class);
    }

    public function test_it_dispatches_multiple_events(): void
    {
        Event::fake();

        Article::factory()->count(3)->create([
            'status'       => 'PENDING',
            'published_at' => now()->subMinute(),
        ]);

        $this->artisan('article:schedule');

        Event::assertDispatched(ArticlePublishedEvent::class, 3);
    }
}
