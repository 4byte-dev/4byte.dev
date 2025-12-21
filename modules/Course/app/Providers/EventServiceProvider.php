<?php

namespace Modules\Course\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Course\Events\CoursePublishedEvent;
use Modules\Course\Events\LessonPublishedEvent;
use Modules\Course\Listeners\CoursePublishedListener;
use Modules\Course\Listeners\LessonPublishedListener;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array<string, array<int, string>>
     */
    protected $listen = [
        CoursePublishedEvent::class => [
            CoursePublishedListener::class,
        ],
        LessonPublishedEvent::class => [
            LessonPublishedListener::class,
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
