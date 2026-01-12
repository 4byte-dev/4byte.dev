<?php

namespace Modules\React\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\React\Events\FollowedEvent;
use Modules\React\Listeners\FollowedListener;
use Modules\React\Listeners\SyncDbListener;
use Modules\React\Listeners\SyncGorseListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        FollowedEvent::class => [
            FollowedListener::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array<string>
     */
    protected $subscribe = [
        SyncGorseListener::class,
        SyncDbListener::class,
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
