<?php

namespace Modules\React\Tests\Unit\Actions;

use Illuminate\Support\Facades\Event;
use Mockery;
use Modules\React\Actions\DeleteCommentAction;
use Modules\React\Events\UserUncommentedEvent;
use Modules\React\Models\Comment;
use Modules\React\Services\ReactService;
use Tests\TestCase;

class DeleteCommentActionTest extends TestCase
{
    public function test_it_caches_delete_comment_and_dispatches_event(): void
    {
        Event::fake();

        $reactService = Mockery::mock(ReactService::class);
        $action       = new DeleteCommentAction($reactService);

        $comment = Mockery::mock(Comment::class);
        $comment->shouldReceive('getAttribute')->with('id')->andReturn(123);
        $comment->shouldReceive('getAttribute')->with('user_id')->andReturn(1);
        $comment->shouldReceive('getAttribute')->with('commentable_type')->andReturn('post');
        $comment->shouldReceive('getAttribute')->with('commentable_id')->andReturn(10);
        $comment->shouldReceive('getAttribute')->with('parent_id')->andReturn(null);

        // Mock magic getters if needed, but getAttribute handles Eloquent access usually.
        // For simple property access on a mock, we might just set public properties if it wasn't an Eloquent model.
        // Since it's typed as Comment, we'll try to set properties if possible, or assume partial mock.

        // Actually, creating a real instance or using a partial mock is better for simpler property access.
        $comment = new Comment([
            'content'          => 'test',
            'user_id'          => 1,
            'commentable_type' => 'post',
            'commentable_id'   => 10,
        ]);
        $comment->id = 123;
        // setParentId null is default.

        $reactService->shouldReceive('cacheDeleteComment')
            ->once()
            ->with('post', 10, 1, null);

        $action->execute($comment);

        Event::assertDispatched(UserUncommentedEvent::class, function ($event) {
            return $event->userId === 1
                && $event->commentableId === 10
                && $event->commentableType === 'post'
                && $event->commentId === 123;
        });
    }
}
