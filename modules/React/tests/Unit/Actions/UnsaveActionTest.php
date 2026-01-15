<?php

namespace Modules\React\Tests\Unit\Actions;

use Illuminate\Support\Facades\Event;
use Mockery;
use Modules\React\Actions\UnsaveAction;
use Modules\React\Events\UserUnsavedEvent;
use Modules\React\Services\ReactService;
use Tests\TestCase;

class UnsaveActionTest extends TestCase
{
    public function test_unsave_action_caches_and_dispatches_event(): void
    {
        Event::fake();

        $service = Mockery::mock(ReactService::class);
        $service->shouldReceive('cacheDeleteSave')
            ->once()
            ->with('Post', 1, 123);

        $action = new UnsaveAction($service);
        $action->execute('Post', 1, 123);

        Event::assertDispatched(UserUnsavedEvent::class, function ($event) {
            return $event->saveableType === 'Post'
                && $event->saveableId === 1
                && $event->userId === 123;
        });
    }
}
