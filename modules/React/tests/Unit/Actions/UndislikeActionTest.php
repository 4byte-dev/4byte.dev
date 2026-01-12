<?php

namespace Modules\React\Tests\Unit\Actions;

use Illuminate\Support\Facades\Event;
use Mockery;
use Modules\React\Actions\UndislikeAction;
use Modules\React\Events\UserUndislikedEvent;
use Modules\React\Services\ReactService;
use Tests\TestCase;

class UndislikeActionTest extends TestCase
{
    public function test_it_removes_dislike_from_cache(): void
    {
        Event::fake();

        $reactService = Mockery::mock(ReactService::class);
        $action       = new UndislikeAction($reactService);

        $type   = 'post';
        $id     = 1;
        $userId = 1;

        $reactService->shouldReceive('cacheDeleteDislike')
            ->once()
            ->with($type, $id, $userId);

        $action->execute($type, $id, $userId);

        Event::assertDispatched(UserUndislikedEvent::class, function ($event) use ($type, $id, $userId) {
            return $event->userId === $userId
                && $event->dislikeableId === $id
                && $event->dislikeableType === $type;
        });
    }
}
