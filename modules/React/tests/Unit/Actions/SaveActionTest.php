<?php

namespace Modules\React\Tests\Unit\Actions;

use Illuminate\Support\Facades\Event;
use Mockery;
use Modules\React\Actions\SaveAction;
use Modules\React\Events\UserSavedEvent;
use Modules\React\Services\ReactService;
use Tests\TestCase;

class SaveActionTest extends TestCase
{
    public function test_save_action_caches_and_dispatches_event(): void
    {
        Event::fake();

        $service = Mockery::mock(ReactService::class);
        $service->shouldReceive('cacheSave')
            ->once()
            ->with('Post', 1, 123);

        $action = new SaveAction($service);
        $action->execute('Post', 1, 123);

        Event::assertDispatched(UserSavedEvent::class, function ($event) {
            return $event->saveableType === 'Post'
                && $event->saveableId === 1
                && $event->userId === 123;
        });
    }
}
