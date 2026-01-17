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
use Modules\React\Services\ReactService;

class SyncDbListener implements ShouldQueue
{
    use InteractsWithQueue;

    protected ReactService $reactService;

    public function __construct(ReactService $reactService) {
        $this->reactService = $reactService;
    }

    /**
     * Handle user liked event.
     */
    public function handleUserLiked(UserLikedEvent $event): void
    {
        $this->reactService->persistDeleteDislike(
            $event->likeableType,
            $event->likeableId,
            $event->userId
        );

        $this->reactService->persistLike(
            $event->likeableType,
            $event->likeableId,
            $event->userId
        );
    }

    /**
     * Handle user unliked event.
     */
    public function handleUserUnliked(UserUnlikedEvent $event): void
    {
        $this->reactService->persistUnlike(
            $event->likeableType,
            $event->likeableId,
            $event->userId
        );
    }

    /**
     * Handle user disliked event.
     */
    public function handleUserDisliked(UserDislikedEvent $event): void
    {
        $this->reactService->persistUnlike(
            $event->dislikeableType,
            $event->dislikeableId,
            $event->userId
        );

        $this->reactService->persistDislike(
            $event->dislikeableType,
            $event->dislikeableId,
            $event->userId
        );
    }

    /**
     * Handle user undisliked event.
     */
    public function handleUserUndisliked(UserUndislikedEvent $event): void
    {
        $this->reactService->persistDeleteDislike(
            $event->dislikeableType,
            $event->dislikeableId,
            $event->userId
        );
    }

    /**
     * Handle user saved event.
     */
    public function handleUserSaved(UserSavedEvent $event): void
    {
        $this->reactService->persistSave(
            $event->saveableType,
            $event->saveableId,
            $event->userId
        );
    }

    /**
     * Handle user unsaved event.
     */
    public function handleUserUnsaved(UserUnsavedEvent $event): void
    {
        $this->reactService->persistDeleteSave(
            $event->saveableType,
            $event->saveableId,
            $event->userId
        );
    }

    /**
     * Handle user followed event.
     */
    public function handleUserFollowed(UserFollowedEvent $event): void
    {
        $this->reactService->persistFollow(
            $event->followableType,
            $event->followableId,
            $event->followerId
        );
    }

    /**
     * Handle user unfollowed event.
     */
    public function handleUserUnfollowed(UserUnfollowedEvent $event): void
    {
        $this->reactService->persistDeleteFollow(
            $event->followableType,
            $event->followableId,
            $event->followerId
        );
    }

    /**
     * Handle user commented event.
     */
    public function handleUserCommented(UserCommentedEvent $event): void
    {
        $this->reactService->persistComment(
            $event->commentableType,
            $event->commentableId,
            $event->content,
            $event->userId,
            $event->parentId
        );
    }

    /**
     * Handle user uncommented event.
     */
    public function handleUserUncommented(UserUncommentedEvent $event): void
    {
        if ($event->commentId) {
            $this->reactService->persistDeleteComment($event->commentId);
        }
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
