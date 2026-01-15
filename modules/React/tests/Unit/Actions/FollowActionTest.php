<?php

namespace Modules\React\Tests\Unit\Actions;

use Illuminate\Support\Facades\Event;
use Mockery;
use Modules\React\Actions\FollowAction;
use Modules\React\Events\UserFollowedEvent;
use Modules\React\Services\ReactService;
use Tests\TestCase;

class FollowActionTest extends TestCase
{
    public function test_it_caches_follow_and_dispatches_event(): void
    {
        Event::fake();

        $reactService = Mockery::mock(ReactService::class);
        $action       = new FollowAction($reactService);

        $type         = 'post';
        $followableId = 1;
        $followerId   = 1;

        $reactService->shouldReceive('cacheFollow')
            ->once()
            ->with($type, $followableId, $followerId);

        $action->execute($type, $followableId, $followerId);

        Event::assertDispatched(UserFollowedEvent::class, function ($event) use ($type, $followableId, $followerId) {
            return $event->followerId === $followerId
                && $event->followableId === $followableId
                && $event->followableType === $type;
        });
    }
}
