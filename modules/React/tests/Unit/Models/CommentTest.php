<?php

namespace Modules\React\Tests\Unit\Models;

use App\Models\User;
use Modules\React\Models\Comment;
use Modules\React\Tests\TestCase;

class CommentTest extends TestCase
{
    public function test_can_instantiate_comment_model(): void
    {
        $target  = User::factory()->create();
        $comment = Comment::factory()->create([
            'commentable_id'   => $target->id,
            'commentable_type' => User::class,
        ]);
        $this->assertInstanceOf(Comment::class, $comment);
    }

    public function test_comment_has_content(): void
    {
        $target  = User::factory()->create();
        $comment = Comment::factory()->create([
            'commentable_id'   => $target->id,
            'commentable_type' => User::class,
        ]);
        $this->assertNotEmpty($comment->content);
    }
}
