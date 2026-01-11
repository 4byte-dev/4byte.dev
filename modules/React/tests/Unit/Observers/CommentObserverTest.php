<?php

namespace Modules\React\Tests\Unit\Observers;

use Mockery;
use Modules\React\Models\Comment;
use Modules\React\Observers\CommentObserver;
use Modules\Recommend\Services\GorseService;
use Tests\TestCase;

class CommentObserverTest extends TestCase
{
    public function test_comment_created_observer_sends_feedback_to_gorse(): void
    {
        $gorseMock = Mockery::mock(GorseService::class);
        $gorseMock->shouldReceive('insertFeedback')->once();

        $observer = new CommentObserver($gorseMock);
        $comment  = Comment::factory()->make();
        $comment->setRelation('commentable', \Modules\User\Models\User::factory()->make(['id' => 1]));

        $observer->created($comment);
    }

    public function test_comment_deleted_observer_sends_feedback_to_gorse(): void
    {
        $gorseMock = Mockery::mock(GorseService::class);
        $gorseMock->shouldReceive('deleteFeedback')->once();

        $observer = new CommentObserver($gorseMock);
        $comment  = Comment::factory()->make();
        $comment->setRelation('commentable', \Modules\User\Models\User::factory()->make(['id' => 1]));

        $observer->deleted($comment);
    }
}
