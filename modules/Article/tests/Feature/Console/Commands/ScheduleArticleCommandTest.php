<?php

namespace Modules\Article\Tests\Feature\Console\Commands;

use Illuminate\Support\Facades\Bus;
use Modules\Article\Jobs\PublishArticleJob;
use Modules\Article\Models\Article;
use Modules\Article\Tests\TestCase;

class ScheduleArticleCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now());
    }

    public function test_it_dispatches_jobs_for_valid_pending_articles(): void
    {
        Bus::fake();

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

        Bus::assertDispatched(PublishArticleJob::class, function ($job) use ($articleToPublish) {
            return $job->article->id === $articleToPublish->id;
        });

        Bus::assertNotDispatched(PublishArticleJob::class, function ($job) use ($articleFuture) {
            return $job->article->id === $articleFuture->id;
        });

        Bus::assertNotDispatched(PublishArticleJob::class, function ($job) use ($articleAlreadyPublished) {
            return $job->article->id === $articleAlreadyPublished->id;
        });
    }

    public function test_it_does_not_dispatch_job_without_publish_date(): void
    {
        Bus::fake();

        $article = Article::factory()->create([
            'status'       => 'PENDING',
            'published_at' => null,
        ]);

        $this->artisan('article:schedule');

        Bus::assertNotDispatched(PublishArticleJob::class);
    }

    public function test_it_dispatches_multiple_jobs(): void
    {
        Bus::fake();

        $articles = Article::factory()->count(3)->create([
            'status'       => 'PENDING',
            'published_at' => now()->subMinute(),
        ]);

        $this->artisan('article:schedule');

        Bus::assertDispatched(PublishArticleJob::class, 3);
    }
}
