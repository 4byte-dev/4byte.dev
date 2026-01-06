<?php

namespace Modules\Page\Tests\Feature\Console\Commands;

use Illuminate\Support\Facades\Event;
use Modules\Page\Events\PagePublishedEvent;
use Modules\Page\Models\Page;
use Modules\Page\Tests\TestCase;

class SchedulePageCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->travelTo(now());
    }

    public function test_it_publishes_valid_pending_pages(): void
    {
        Event::fake();

        $pageToPublish = Page::factory()->create([
            'status'       => 'PENDING',
            'published_at' => now()->subMinute(),
        ]);

        $pageFuture = Page::factory()->create([
            'status'       => 'PENDING',
            'published_at' => now()->addDay(),
        ]);

        $pageAlreadyPublished = Page::factory()->create([
            'status'       => 'PUBLISHED',
            'published_at' => now()->subMinute(),
        ]);

        $this->artisan('page:schedule')
            ->expectsOutput('Pending pages checked')
            ->assertExitCode(0);

        $this->assertEquals('PUBLISHED', $pageToPublish->refresh()->status);
        $this->assertEquals('PENDING', $pageFuture->refresh()->status);
        $this->assertEquals('PUBLISHED', $pageAlreadyPublished->refresh()->status);

        Event::assertDispatched(PagePublishedEvent::class, function ($event) use ($pageToPublish) {
            return $event->page->id === $pageToPublish->id;
        });

        Event::assertDispatchedTimes(PagePublishedEvent::class, 1);

        Event::assertNotDispatched(PagePublishedEvent::class, function ($event) use ($pageFuture) {
            return $event->page->id === $pageFuture->id;
        });

        $this->assertDatabaseHas('pages', [
            'id'     => $pageToPublish->id,
            'status' => 'PUBLISHED',
        ]);
    }

    public function test_it_does_not_publish_pages_without_publish_date(): void
    {
        Event::fake();

        $page = Page::factory()->create([
            'status'       => 'PENDING',
            'published_at' => null,
        ]);

        $this->artisan('page:schedule');

        $this->assertEquals('PENDING', $page->refresh()->status);
        Event::assertNotDispatched(PagePublishedEvent::class);
    }

    public function test_it_publishes_multiple_pending_pages(): void
    {
        Event::fake();

        $pages = Page::factory()->count(3)->create([
            'status'       => 'PENDING',
            'published_at' => now()->subMinute(),
        ]);

        $this->artisan('page:schedule');

        foreach ($pages as $page) {
            $this->assertEquals('PUBLISHED', $page->refresh()->status);
        }

        Event::assertDispatchedTimes(PagePublishedEvent::class, 3);
    }

    public function test_command_is_idempotent(): void
    {
        Event::fake();

        $page = Page::factory()->create([
            'status'       => 'PENDING',
            'published_at' => now()->subMinute(),
        ]);

        $this->artisan('page:schedule');
        $this->artisan('page:schedule');

        Event::assertDispatchedTimes(PagePublishedEvent::class, 1);
        $this->assertEquals('PUBLISHED', $page->refresh()->status);
    }
}
