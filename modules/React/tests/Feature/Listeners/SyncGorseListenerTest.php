<?php

namespace Modules\React\Tests\Feature\Listeners;

use Mockery;
use Modules\React\Events\UserLikedEvent;
use Modules\React\Events\UserUnlikedEvent;
use Modules\React\Listeners\SyncGorseListener;
use Modules\Recommend\Services\GorseService;
use Modules\Recommend\Classes\GorseFeedback;
use Tests\TestCase;

class SyncGorseListenerTest extends TestCase
{
    public function test_it_handles_user_liked_event_and_inserts_feedback()
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
        $event = new UserLikedEvent(1, 'App\Models\Article', 100);

        $listener->handleUserLiked($event, $gorseService);
    }

    public function test_it_handles_user_unliked_event_and_deletes_feedback()
    {
        $gorseService = Mockery::mock(GorseService::class);
        $gorseService->shouldReceive('deleteFeedback')
            ->once()
            ->with('like', '1', 'article:100');

        $listener = new SyncGorseListener();
        $event = new UserUnlikedEvent(1, 'App\Models\Article', 100);

        $listener->handleUserUnliked($event, $gorseService);
    }
}
