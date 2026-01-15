<?php

namespace Modules\React\Tests\Unit\Actions;

use Illuminate\Support\Facades\Event;
use Mockery;
use Modules\React\Actions\UnfollowAction;
use Modules\React\Events\UserUnfollowedEvent;
use Modules\React\Services\ReactService;
use Tests\TestCase;

class UnfollowActionTest extends TestCase
{
    public function test_it_caches_unfollow_and_dispatches_event(): void
    {
        Event::fake();

        $reactService = Mockery::mock(ReactService::class);
        $action       = new UnfollowAction($reactService);

        $type         = 'post';
        $followableId = 1;
        $followerId   = 1;

        $reactService->shouldReceive('cacheDeleteFollow')
            ->once()
            ->with($type, $followableId, $followerId);

        $action->execute($type, $followableId, $followerId);

        Event::assertDispatched(UserUnfollowedEvent::class, function ($event) use ($type, $followableId, $followerId) {
            return $event->followerId === $followerId
                && $event->followableId === $followableId
                && $event->followableType === $type;
        });
    }
}
