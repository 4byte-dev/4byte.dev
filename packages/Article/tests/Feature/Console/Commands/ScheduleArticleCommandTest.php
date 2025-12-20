<?php

namespace Packages\Article\Tests\Feature\Console\Commands;

use Illuminate\Support\Facades\Event;
use Packages\Article\Events\ArticlePublishedEvent;
use Packages\Article\Models\Article;
use Packages\Article\Tests\TestCase;

class ScheduleArticleCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now());
    }

    public function test_it_publishes_valid_pending_articles(): void
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

        $this->assertEquals('PUBLISHED', $articleToPublish->refresh()->status);
        $this->assertEquals('PENDING', $articleFuture->refresh()->status);
        $this->assertEquals('PUBLISHED', $articleAlreadyPublished->refresh()->status);

        Event::assertDispatched(ArticlePublishedEvent::class, function ($event) use ($articleToPublish) {
            return $event->article->id === $articleToPublish->id;
        });

        Event::assertDispatchedTimes(ArticlePublishedEvent::class, 1);

        Event::assertNotDispatched(ArticlePublishedEvent::class, function ($event) use ($articleFuture) {
            return $event->article->id === $articleFuture->id;
        });

        $this->assertDatabaseHas('articles', [
            'id'     => $articleToPublish->id,
            'status' => 'PUBLISHED',
        ]);
    }

    public function test_it_does_not_publish_articles_without_publish_date(): void
    {
        Event::fake();

        $article = Article::factory()->create([
            'status'       => 'PENDING',
            'published_at' => null,
        ]);

        $this->artisan('article:schedule');

        $this->assertEquals('PENDING', $article->refresh()->status);
        Event::assertNotDispatched(ArticlePublishedEvent::class);
    }

    public function test_it_publishes_multiple_pending_articles(): void
    {
        Event::fake();

        $articles = Article::factory()->count(3)->create([
            'status'       => 'PENDING',
            'published_at' => now()->subMinute(),
        ]);

        $this->artisan('article:schedule');

        foreach ($articles as $article) {
            $this->assertEquals('PUBLISHED', $article->refresh()->status);
        }

        Event::assertDispatchedTimes(ArticlePublishedEvent::class, 3);
    }

    public function test_command_is_idempotent(): void
    {
        Event::fake();

        $article = Article::factory()->create([
            'status'       => 'PENDING',
            'published_at' => now()->subMinute(),
        ]);

        $this->artisan('article:schedule');
        $this->artisan('article:schedule');

        Event::assertDispatchedTimes(ArticlePublishedEvent::class, 1);
        $this->assertEquals('PUBLISHED', $article->refresh()->status);
    }
}
