<?php

namespace Modules\React\Tests\Feature\Listeners;

use Mockery;
use Modules\React\Events\UserCommentedEvent;
use Modules\React\Events\UserDislikedEvent;
use Modules\React\Events\UserFollowedEvent;
use Modules\React\Events\UserLikedEvent;
use Modules\React\Events\UserUncommentedEvent;
use Modules\React\Events\UserUndislikedEvent;
use Modules\React\Events\UserUnfollowedEvent;
use Modules\React\Events\UserUnlikedEvent;
use Modules\React\Listeners\SyncGorseListener;
use Modules\Recommend\Classes\GorseFeedback;
use Modules\Recommend\Services\GorseService;
use Tests\TestCase;

class SyncGorseListenerTest extends TestCase
{
    public function test_it_handles_user_liked_event_and_inserts_feedback(): void
    {
        $gorseService = Mockery::mock(GorseService::class);
        $gorseService->shouldReceive('insertFeedback')
            ->once()
            ->with(Mockery::on(function ($feedback) {
                return $feedback instanceof GorseFeedback
                    && $feedback->getFeedbackType() === 'like'
                    && $feedback->getUserId() === '1'
                    && $feedback->getItemId() === 'article:100';
            }));

        $listener = new SyncGorseListener();
        $event    = new UserLikedEvent(1, 'App\Models\Article', 100);

        $listener->handleUserLiked($event, $gorseService);
    }

    public function test_it_handles_user_unliked_event_and_deletes_feedback(): void
    {
        $gorseService = Mockery::mock(GorseService::class);
        $gorseService->shouldReceive('deleteFeedback')
            ->once()
            ->with('like', '1', 'article:100');

        $listener = new SyncGorseListener();
        $event    = new UserUnlikedEvent(1, 'App\Models\Article', 100);

        $listener->handleUserUnliked($event, $gorseService);
    }

    public function test_it_handles_user_disliked_event_and_inserts_feedback(): void
    {
        $gorseService = Mockery::mock(GorseService::class);
        $gorseService->shouldReceive('insertFeedback')
            ->once()
            ->with(Mockery::on(function ($feedback) {
                return $feedback instanceof GorseFeedback
                    && $feedback->getFeedbackType() === 'dislike'
                    && $feedback->getUserId() === '1'
                    && $feedback->getItemId() === 'article:100';
            }));

        $listener = new SyncGorseListener();
        $event    = new UserDislikedEvent(1, 'App\Models\Article', 100);

        $listener->handleUserDisliked($event, $gorseService);
    }

    public function test_it_handles_user_undisliked_event_and_deletes_feedback(): void
    {
        $gorseService = Mockery::mock(GorseService::class);
        $gorseService->shouldReceive('deleteFeedback')
            ->once()
            ->with('dislike', '1', 'article:100');

        $listener = new SyncGorseListener();
        $event    = new UserUndislikedEvent(1, 'App\Models\Article', 100);

        $listener->handleUserUndisliked($event, $gorseService);
    }

    public function test_it_handles_user_followed_event_and_inserts_feedback(): void
    {
        $gorseService = Mockery::mock(GorseService::class);
        $gorseService->shouldReceive('insertFeedback')
            ->once()
            ->with(Mockery::on(function ($feedback) {
                return $feedback instanceof GorseFeedback
                    && $feedback->getFeedbackType() === 'subscribe'
                    && $feedback->getUserId() === '1'
                    && $feedback->getItemId() === 'user:2';
            }));

        $listener = new SyncGorseListener();
        $event    = new UserFollowedEvent(1, 'App\Models\User', 2);

        $listener->handleUserFollowed($event, $gorseService);
    }

    public function test_it_handles_user_unfollowed_event_and_deletes_feedback(): void
    {
        $gorseService = Mockery::mock(GorseService::class);
        $gorseService->shouldReceive('deleteFeedback')
            ->once()
            ->with('subscribe', '1', 'user:2');

        $listener = new SyncGorseListener();
        $event    = new UserUnfollowedEvent(1, 'App\Models\User', 2);

        $listener->handleUserUnfollowed($event, $gorseService);
    }

    public function test_it_handles_user_commented_event_and_inserts_feedback(): void
    {
        $gorseService = Mockery::mock(GorseService::class);
        $gorseService->shouldReceive('insertFeedback')
            ->once()
            ->with(Mockery::on(function ($feedback) {
                return $feedback instanceof GorseFeedback
                    && $feedback->getFeedbackType() === 'comment'
                    && $feedback->getUserId() === '1'
                    && $feedback->getItemId() === 'article:100';
            }));

        $listener = new SyncGorseListener();
        $event    = new UserCommentedEvent(1, 'App\Models\Article', 100, 'content');

        $listener->handleUserCommented($event, $gorseService);
    }

    public function test_it_handles_user_uncommented_event_and_deletes_feedback(): void
    {
        $gorseService = Mockery::mock(GorseService::class);
        $gorseService->shouldReceive('deleteFeedback')
            ->once()
            ->with('comment', '1', 'article:100');

        $listener = new SyncGorseListener();
        $event    = new UserUncommentedEvent(1, 'App\Models\Article', 100, 50);

        $listener->handleUserUncommented($event, $gorseService);
    }
}
