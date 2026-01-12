<?php

namespace Modules\React\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Events\Dispatcher;
use Modules\React\Events\UserLikedEvent;
use Modules\React\Events\UserUnlikedEvent;
use Modules\React\Services\ReactService;

class SyncDbListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle user liked event.
     */
    public function handleUserLiked(UserLikedEvent $event, ReactService $reactService): void
    {
        $reactService->persistDeleteDislike(
            $event->likeableType,
            $event->likeableId,
            $event->userId
        );

        $reactService->persistLike(
            $event->likeableType,
            $event->likeableId,
            $event->userId
        );
    }

    /**
     * Handle user unliked event.
     */
    public function handleUserUnliked(UserUnlikedEvent $event, ReactService $reactService): void
    {
        $reactService->persistUnlike(
            $event->likeableType,
            $event->likeableId,
            $event->userId
        );
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher  $events
     */
    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            UserLikedEvent::class,
            [self::class, 'handleUserLiked']
        );

        $events->listen(
            UserUnlikedEvent::class,
            [self::class, 'handleUserUnliked']
        );
    }
}
