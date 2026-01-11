<?php

namespace Modules\Tag\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Article\Events\ArticleDeletedEvent;
use Modules\Article\Events\ArticlePublishedEvent;
use Modules\Tag\Listeners\ArticleDeletedListener;
use Modules\Tag\Listeners\ArticlePublishedListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        ArticlePublishedEvent::class => [
            ArticlePublishedListener::class,
        ],
        ArticleDeletedEvent::class => [
            ArticleDeletedListener::class,
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
