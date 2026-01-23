<?php

namespace Modules\React\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Queue\InteractsWithQueue;
use Modules\React\Events\UserCommentedEvent;
use Modules\React\Events\UserDislikedEvent;
use Modules\React\Events\UserFollowedEvent;
use Modules\React\Events\UserLikedEvent;
use Modules\React\Events\UserSavedEvent;
use Modules\React\Events\UserUncommentedEvent;
use Modules\React\Events\UserUndislikedEvent;
use Modules\React\Events\UserUnfollowedEvent;
use Modules\React\Events\UserUnlikedEvent;
use Modules\React\Events\UserUnsavedEvent;
use Modules\Recommend\Classes\GorseFeedback;
use Modules\Recommend\Services\GorseService;

class ReactSyncGorseListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected GorseService $gorseService;

    public function __construct(GorseService $gorseService)
    {
        $this->gorseService = $gorseService;
    }

    /**
     * Handle user liked event.
     */
    public function handleUserLiked(UserLikedEvent $event): void
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

        $this->gorseService->insertFeedback($feedback);
    }

    /**
     * Handle user unliked event.
     */
    public function handleUserUnliked(UserUnlikedEvent $event): void
    {
        $type   = strtolower(class_basename($event->likeableType));
        $itemId = "{$type}:{$event->likeableId}";
        $userId = (string) $event->userId;

        $this->gorseService->deleteFeedback('like', $userId, $itemId);
    }

    /**
     * Handle user disliked event.
     */
    public function handleUserDisliked(UserDislikedEvent $event): void
    {
        $type   = strtolower(class_basename($event->dislikeableType));
        $itemId = "{$type}:{$event->dislikeableId}";
        $userId = (string) $event->userId;

        $feedback = new GorseFeedback(
            'dislike',
            $userId,
            $itemId,
            '',
            now()->toDateTimeString()
        );

        $this->gorseService->insertFeedback($feedback);
    }

    /**
     * Handle user undisliked event.
     */
    public function handleUserUndisliked(UserUndislikedEvent $event): void
    {
        $type   = strtolower(class_basename($event->dislikeableType));
        $itemId = "{$type}:{$event->dislikeableId}";
        $userId = (string) $event->userId;

        $this->gorseService->deleteFeedback('dislike', $userId, $itemId);
    }

    /**
     * Handle user saved event.
     */
    public function handleUserSaved(UserSavedEvent $event): void
    {
        $type   = strtolower(class_basename($event->saveableType));
        $itemId = "{$type}:{$event->saveableId}";
        $userId = (string) $event->userId;

        $feedback = new GorseFeedback(
            'star',
            $userId,
            $itemId,
            '',
            now()->toDateTimeString()
        );

        $this->gorseService->insertFeedback($feedback);
    }

    /**
     * Handle user unsaved event.
     */
    public function handleUserUnsaved(UserUnsavedEvent $event): void
    {
        $type   = strtolower(class_basename($event->saveableType));
        $itemId = "{$type}:{$event->saveableId}";
        $userId = (string) $event->userId;

        $this->gorseService->deleteFeedback('star', $userId, $itemId);
    }

    /**
     * Handle user followed event.
     */
    public function handleUserFollowed(UserFollowedEvent $event): void
    {
        $type   = strtolower(class_basename($event->followableType));
        $itemId = "{$type}:{$event->followableId}";
        $userId = (string) $event->followerId;

        $feedback = new GorseFeedback(
            'subscribe',
            $userId,
            $itemId,
            '',
            now()->toDateTimeString()
        );

        $this->gorseService->insertFeedback($feedback);
    }

    /**
     * Handle user unfollowed event.
     */
    public function handleUserUnfollowed(UserUnfollowedEvent $event): void
    {
        $type   = strtolower(class_basename($event->followableType));
        $itemId = "{$type}:{$event->followableId}";
        $userId = (string) $event->followerId;

        $this->gorseService->deleteFeedback('subscribe', $userId, $itemId);
    }

    /**
     * Handle user commented event.
     */
    public function handleUserCommented(UserCommentedEvent $event): void
    {
        $type   = strtolower(class_basename($event->commentableType));
        $itemId = "{$type}:{$event->commentableId}";
        $userId = (string) $event->userId;

        $feedback = new GorseFeedback(
            'comment',
            $userId,
            $itemId,
            '',
            now()->toDateTimeString()
        );

        $this->gorseService->insertFeedback($feedback);
    }

    /**
     * Handle user uncommented event.
     */
    public function handleUserUncommented(UserUncommentedEvent $event): void
    {
        $type   = strtolower(class_basename($event->commentableType));
        $itemId = "{$type}:{$event->commentableId}";
        $userId = (string) $event->userId;

        $this->gorseService->deleteFeedback('comment', $userId, $itemId);
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

        $events->listen(
            UserSavedEvent::class,
            [self::class, 'handleUserSaved']
        );

        $events->listen(
            UserUnsavedEvent::class,
            [self::class, 'handleUserUnsaved']
        );

        $events->listen(
            UserFollowedEvent::class,
            [self::class, 'handleUserFollowed']
        );

        $events->listen(
            UserUnfollowedEvent::class,
            [self::class, 'handleUserUnfollowed']
        );

        $events->listen(
            UserCommentedEvent::class,
            [self::class, 'handleUserCommented']
        );

        $events->listen(
            UserUncommentedEvent::class,
            [self::class, 'handleUserUncommented']
        );
    }
}
