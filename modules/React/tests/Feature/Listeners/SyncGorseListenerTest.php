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
    /** @var Mockery\MockInterface&GorseService */
    protected GorseService $gorseService;

    protected SyncGorseListener $listener;

    public function setUp(): void
    {
        $this->gorseService = Mockery::mock(GorseService::class);
        $this->listener     = new SyncGorseListener($this->gorseService);
        parent::setUp();
    }

    public function test_it_handles_user_liked_event_and_inserts_feedback(): void
    {
        $this->gorseService->shouldReceive('insertFeedback')
            ->once()
            ->with(Mockery::on(function ($feedback) {
                return $feedback instanceof GorseFeedback
                    && $feedback->getFeedbackType() === 'like'
                    && $feedback->getUserId() === '1'
                    && $feedback->getItemId() === 'article:100';
            }));

        $event    = new UserLikedEvent(1, 'App\Models\Article', 100);

        $this->listener->handleUserLiked($event);
    }

    public function test_it_handles_user_unliked_event_and_deletes_feedback(): void
    {
        $this->gorseService->shouldReceive('deleteFeedback')
            ->once()
            ->with('like', '1', 'article:100');

        $event    = new UserUnlikedEvent(1, 'App\Models\Article', 100);

        $this->listener->handleUserUnliked($event);
    }

    public function test_it_handles_user_disliked_event_and_inserts_feedback(): void
    {
        $this->gorseService->shouldReceive('insertFeedback')
            ->once()
            ->with(Mockery::on(function ($feedback) {
                return $feedback instanceof GorseFeedback
                    && $feedback->getFeedbackType() === 'dislike'
                    && $feedback->getUserId() === '1'
                    && $feedback->getItemId() === 'article:100';
            }));

        $event    = new UserDislikedEvent(1, 'App\Models\Article', 100);

        $this->listener->handleUserDisliked($event);
    }

    public function test_it_handles_user_undisliked_event_and_deletes_feedback(): void
    {
        $this->gorseService->shouldReceive('deleteFeedback')
            ->once()
            ->with('dislike', '1', 'article:100');

        $event    = new UserUndislikedEvent(1, 'App\Models\Article', 100);

        $this->listener->handleUserUndisliked($event);
    }

    public function test_it_handles_user_followed_event_and_inserts_feedback(): void
    {
        $this->gorseService->shouldReceive('insertFeedback')
            ->once()
            ->with(Mockery::on(function ($feedback) {
                return $feedback instanceof GorseFeedback
                    && $feedback->getFeedbackType() === 'subscribe'
                    && $feedback->getUserId() === '1'
                    && $feedback->getItemId() === 'user:2';
            }));

        $event    = new UserFollowedEvent(1, 'App\Models\User', 2);

        $this->listener->handleUserFollowed($event);
    }

    public function test_it_handles_user_unfollowed_event_and_deletes_feedback(): void
    {
        $this->gorseService->shouldReceive('deleteFeedback')
            ->once()
            ->with('subscribe', '1', 'user:2');

        $event    = new UserUnfollowedEvent(1, 'App\Models\User', 2);

        $this->listener->handleUserUnfollowed($event);
    }

    public function test_it_handles_user_commented_event_and_inserts_feedback(): void
    {
        $this->gorseService->shouldReceive('insertFeedback')
            ->once()
            ->with(Mockery::on(function ($feedback) {
                return $feedback instanceof GorseFeedback
                    && $feedback->getFeedbackType() === 'comment'
                    && $feedback->getUserId() === '1'
                    && $feedback->getItemId() === 'article:100';
            }));

        $event    = new UserCommentedEvent(1, 'App\Models\Article', 100, 'content');

        $this->listener->handleUserCommented($event);
    }

    public function test_it_handles_user_uncommented_event_and_deletes_feedback(): void
    {
        $this->gorseService->shouldReceive('deleteFeedback')
            ->once()
            ->with('comment', '1', 'article:100');

        $event    = new UserUncommentedEvent(1, 'App\Models\Article', 100, 50);

        $this->listener->handleUserUncommented($event);
    }
}
