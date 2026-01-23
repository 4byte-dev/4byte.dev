<?php

namespace Modules\React\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\React\Listeners\ReactSyncDbListener;
use Modules\React\Listeners\ReactSyncGorseListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The subscriber classes to register.
     *
     * @var array<string>
     */
    protected $subscribe = [
        ReactSyncGorseListener::class,
        ReactSyncDbListener::class,
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
