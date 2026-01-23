<?php

namespace Modules\Article\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Article\Events\ArticlePublishedEvent;
use Modules\Article\Listeners\ArticlePublishedListener;
use Modules\Article\Listeners\ArticleSyncGorseListener;

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
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array<string>
     */
    protected $subscribe = [
        ArticleSyncGorseListener::class,
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
