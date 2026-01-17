<?php

namespace Modules\React\Tests\Unit\Actions;

use Illuminate\Support\Facades\Event;
use Mockery;
use Modules\React\Actions\CommentAction;
use Modules\React\Events\UserCommentedEvent;
use Modules\React\Services\ReactService;
use Tests\TestCase;

class CommentActionTest extends TestCase
{
    public function test_it_caches_comment_and_dispatches_event(): void
    {
        Event::fake();

        $reactService = Mockery::mock(ReactService::class);
        $action       = new CommentAction($reactService);

        $type     = 'post';
        $id       = 1;
        $content  = 'This is a comment content';
        $userId   = 1;
        $parentId = null;

        $reactService->shouldReceive('cacheComment')
            ->once()
            ->with($type, $id, $userId, $parentId);

        $action->execute($type, $id, $content, $userId, $parentId);

        Event::assertDispatched(UserCommentedEvent::class, function ($event) use ($type, $id, $userId, $content, $parentId) {
            return $event->userId === $userId
                && $event->commentableId === $id
                && $event->commentableType === $type
                && $event->content === $content
                && $event->parentId === $parentId;
        });
    }

    public function test_it_caches_reply_and_dispatches_event(): void
    {
        Event::fake();

        $reactService = Mockery::mock(ReactService::class);
        $action       = new CommentAction($reactService);

        $type     = 'post';
        $id       = 1;
        $content  = 'This is a reply content';
        $userId   = 1;
        $parentId = 5;

        $reactService->shouldReceive('cacheComment')
            ->once()
            ->with($type, $id, $userId, $parentId);

        $action->execute($type, $id, $content, $userId, $parentId);

        Event::assertDispatched(UserCommentedEvent::class, function ($event) use ($parentId) {
            return $event->parentId === $parentId;
        });
    }
}
