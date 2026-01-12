<?php

namespace Modules\React\Tests\Unit\Actions;

use Illuminate\Support\Facades\Event;
use Mockery;
use Modules\React\Actions\DislikeAction;
use Modules\React\Events\UserDislikedEvent;
use Modules\React\Services\ReactService;
use Tests\TestCase;

class DislikeActionTest extends TestCase
{
    public function test_it_removes_existing_like_from_cache_before_disliking()
    {
        Event::fake();

        $reactService = Mockery::mock(ReactService::class);
        $action       = new DislikeAction($reactService);

        $type   = 'post';
        $id     = 1;
        $userId = 1;

        $reactService->shouldReceive('checkLiked')
            ->once()
            ->with($type, $id, $userId)
            ->andReturn(true);

        $reactService->shouldReceive('cacheUnlike')
            ->once()
            ->with($type, $id, $userId);
        $reactService->shouldReceive('cacheDislike')
            ->once()
            ->with($type, $id, $userId);

        $action->execute($type, $id, $userId);

        Event::assertDispatched(UserDislikedEvent::class, function ($event) use ($type, $id, $userId) {
            return $event->userId === $userId
                && $event->dislikeableId === $id
                && $event->dislikeableType === $type;
        });
    }

    public function test_it_does_not_remove_like_if_not_present()
    {
        Event::fake();

        $reactService = Mockery::mock(ReactService::class);
        $action       = new DislikeAction($reactService);

        $type   = 'post';
        $id     = 1;
        $userId = 1;

        $reactService->shouldReceive('checkLiked')
            ->once()
            ->with($type, $id, $userId)
            ->andReturn(false);
        $reactService->shouldNotReceive('cacheUnlike');
        $reactService->shouldReceive('cacheDislike')
            ->once()
            ->with($type, $id, $userId);

        $action->execute($type, $id, $userId);

        Event::assertDispatched(UserDislikedEvent::class);
    }
}
