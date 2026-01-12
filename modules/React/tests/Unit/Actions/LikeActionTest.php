<?php

namespace Modules\React\Tests\Unit\Actions;

use Modules\React\Actions\LikeAction;
use Modules\React\Services\ReactService;
use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\Event;
use Modules\React\Events\UserLikedEvent;

class LikeActionTest extends TestCase
{
    public function test_it_removes_existing_dislike_from_cache_before_liking()
    {
        Event::fake();

        $reactService = Mockery::mock(ReactService::class);
        $action = new LikeAction($reactService);

        $type = 'post';
        $id = 1;
        $userId = 1;

        $reactService->shouldReceive('checkDisliked')
            ->once()
            ->with($type, $id, $userId)
            ->andReturn(true);

        $reactService->shouldReceive('cacheDeleteDislike')
            ->once()
            ->with($type, $id, $userId);
        $reactService->shouldReceive('cacheLike')
            ->once()
            ->with($type, $id, $userId);

        $action->execute($type, $id, $userId);

        Event::assertDispatched(UserLikedEvent::class, function ($event) use ($type, $id, $userId) {
            return $event->userId === $userId 
                && $event->likeableId === $id 
                && $event->likeableType === $type;
        });
    }

    public function test_it_does_not_remove_dislike_if_not_present()
    {
        Event::fake();

        $reactService = Mockery::mock(ReactService::class);
        $action = new LikeAction($reactService);

        $type = 'post';
        $id = 1;
        $userId = 1;

        $reactService->shouldReceive('checkDisliked')
            ->once()
            ->with($type, $id, $userId)
            ->andReturn(false);
        $reactService->shouldNotReceive('cacheDeleteDislike');
        $reactService->shouldReceive('cacheLike')
            ->once()
            ->with($type, $id, $userId);

        $action->execute($type, $id, $userId);
        
        Event::assertDispatched(UserLikedEvent::class);
    }
}
