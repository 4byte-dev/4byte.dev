<?php

namespace Modules\News\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\News\Events\NewsDeletedEvent;
use Modules\News\Events\NewsPublishedEvent;
use Modules\News\Listeners\NewsDeletedListener;
use Modules\News\Listeners\NewsPublishedListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        NewsPublishedEvent::class => [
            NewsPublishedListener::class,
        ],
        NewsDeletedEvent::class => [
            NewsDeletedListener::class,
        ],
    ];

    /**
     * Indicates if events should be discovered.
     *
     * @var bool
     */
    protected static $shouldDiscoverEvents = true;

    /**
     * Configure the proper event listeners for email verification.
     */
    protected function configureEmailVerification(): void
    {
    }
}
