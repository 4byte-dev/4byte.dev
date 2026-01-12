<?php

namespace Modules\React\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;
use Modules\React\Events\UserDislikedEvent;
use Modules\React\Events\UserLikedEvent;
use Modules\React\Events\UserUndislikedEvent;
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
     * Handle user disliked event.
     */
    public function handleUserDisliked(UserDislikedEvent $event, ReactService $reactService): void
    {
        $reactService->persistUnlike(
            $event->dislikeableType,
            $event->dislikeableId,
            $event->userId
        );

        $reactService->persistDislike(
            $event->dislikeableType,
            $event->dislikeableId,
            $event->userId
        );
    }

    /**
     * Handle user undisliked event.
     */
    public function handleUserUndisliked(UserUndislikedEvent $event, ReactService $reactService): void
    {
        $reactService->persistDeleteDislike(
            $event->dislikeableType,
            $event->dislikeableId,
            $event->userId
        );
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
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

        $events->listen(
            UserDislikedEvent::class,
            [self::class, 'handleUserDisliked']
        );

        $events->listen(
            UserUndislikedEvent::class,
            [self::class, 'handleUserUndisliked']
        );
    }
}
