<?php

namespace Modules\React\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;
use Modules\React\Events\UserLikedEvent;
use Modules\React\Events\UserUnlikedEvent;
use Modules\Recommend\Classes\GorseFeedback; // Assuming this is the correct namespace based on file view
use Modules\Recommend\Services\GorseService;

class SyncGorseListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle user liked event.
     */
    public function handleUserLiked(UserLikedEvent $event, GorseService $gorse): void
    {
        $type   = strtolower(class_basename($event->likeableType));
        $itemId = "{$type}:{$event->likeableId}";

        $userId = (string) $event->userId;

        $feedback = new GorseFeedback(
            'like',
            $userId,
            $itemId,
            '',
            now()->toDateTimeString()
        );

        $gorse->insertFeedback($feedback);
    }

    /**
     * Handle user unliked event.
     */
    public function handleUserUnliked(UserUnlikedEvent $event, GorseService $gorse): void
    {
        $type   = strtolower(class_basename($event->likeableType));
        $itemId = "{$type}:{$event->likeableId}";
        $userId = (string) $event->userId;

        $gorse->deleteFeedback('like', $userId, $itemId);
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
    }
}
