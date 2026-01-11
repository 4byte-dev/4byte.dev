<?php

namespace Modules\React\Tests\Unit\Database\Factories;

use Modules\React\Database\Factories\CommentFactory;
use Modules\React\Models\Comment;
use Modules\React\Tests\TestCase;

class CommentFactoryTest extends TestCase
{
    public function test_can_create_comment_instance(): void
    {
        $factory = CommentFactory::new();
        $this->assertInstanceOf(CommentFactory::class, $factory);
    }

    public function test_can_create_comment(): void
    {
        $target  = \Modules\User\Models\User::factory()->create();
        $comment = Comment::factory()->create([
            'commentable_id'   => $target->id,
            'commentable_type' => \Modules\User\Models\User::class,
        ]);
        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
        ]);
    }
}
