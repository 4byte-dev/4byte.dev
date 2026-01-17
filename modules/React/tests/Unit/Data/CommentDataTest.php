<?php

namespace Modules\React\Tests\Unit\Data;

use Illuminate\Support\Facades\Auth;
use Mockery;
use Modules\React\Data\CommentData;
use Modules\React\Mappers\CommentMapper;
use Modules\React\Models\Comment;
use Modules\User\Data\UserData;
use Modules\User\Models\User;
use Modules\User\Services\UserService;
use Tests\TestCase;

class CommentDataTest extends TestCase
{
    public function test_can_create_comment_data(): void
    {
        $user            = User::factory()->create();
        $userServiceMock = Mockery::mock(UserService::class);
        $userServiceMock->shouldReceive('getData')
            ->once()
            ->with($user->id)
            ->andReturn(new UserData(
                id: $user->id,
                username: $user->username,
                name: $user->name,
                avatar: "",
                followers: 0,
                followings: 0,
                isFollowing: false,
                created_at: $user->created_at
            ));

        $this->app->instance(UserService::class, $userServiceMock);

        $gorseMock = Mockery::mock(\Modules\Recommend\Services\GorseService::class);
        $gorseMock->shouldReceive('insertFeedback')->andReturnNull();
        $this->app->instance(\Modules\Recommend\Services\GorseService::class, $gorseMock);

        $comment = Comment::factory()->create([
            'user_id'          => $user->id,
            'commentable_id'   => $user->id,
            'commentable_type' => User::class,
        ]);

        Auth::shouldReceive('id')->andReturn(null);

        $commentData = CommentMapper::toData($comment);

        $this->assertInstanceOf(CommentData::class, $commentData);
        $this->assertEquals($comment->content, $commentData->content);
        $this->assertEquals($user->id, $commentData->user->id);
    }
}
