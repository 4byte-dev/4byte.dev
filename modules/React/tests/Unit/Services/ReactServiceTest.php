<?php

namespace Modules\React\Tests\Unit\Services;

use Modules\React\Services\ReactService;
use Modules\User\Models\User;
use Tests\TestCase;

class ReactServiceTest extends TestCase
{
    protected ReactService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ReactService();
    }

    public function test_can_persist_and_persist_unlike_like(): void
    {
        $user         = User::factory()->create();
        $likeableId   = $user->id;
        $likeableType = User::class;

        $this->service->persistLike($likeableType, $likeableId, $user->id);

        $this->assertDatabaseHas('likes', [
            'user_id'       => $user->id,
            'likeable_id'   => $likeableId,
            'likeable_type' => $likeableType,
        ]);
        $this->assertEquals(1, $this->service->getCount($likeableType, $likeableId, 'likes'));

        $this->assertDatabaseHas('counts', [
            'countable_id'   => $likeableId,
            'countable_type' => $likeableType,
            'filter'         => 'likes',
            'count'          => 1,
        ]);

        $this->service->persistUnlike($likeableType, $likeableId, $user->id);

        $this->assertDatabaseMissing('likes', [
            'user_id'       => $user->id,
            'likeable_id'   => $likeableId,
            'likeable_type' => $likeableType,
        ]);

        $this->assertDatabaseHas('counts', [
            'countable_id'   => $likeableId,
            'countable_type' => $likeableType,
            'filter'         => 'likes',
            'count'          => 0,
        ]);
    }

    public function test_can_cache_and_cache_unlike_like(): void
    {
        $user         = User::factory()->create();
        $likeableId   = $user->id;
        $likeableType = User::class;

        $this->service->cacheLike($likeableType, $likeableId, $user->id);

        $this->assertTrue($this->service->checkLiked($likeableType, $likeableId, $user->id));
        $this->assertEquals(1, $this->service->getLikesCount($likeableType, $likeableId));

        $this->service->cacheUnlike($likeableType, $likeableId, $user->id);

        $this->assertFalse($this->service->checkLiked($likeableType, $likeableId, $user->id));
        $this->assertEquals(0, $this->service->getLikesCount($likeableType, $likeableId));
    }

    public function test_can_persist_and_persist_delete_dislike(): void
    {
        $user            = User::factory()->create();
        $dislikeableId   = $user->id;
        $dislikeableType = User::class;

        $this->service->persistDislike($dislikeableType, $dislikeableId, $user->id);

        $this->assertDatabaseHas('dislikes', [
            'user_id'          => $user->id,
            'dislikeable_id'   => $dislikeableId,
            'dislikeable_type' => $dislikeableType,
        ]);
        $this->assertEquals(1, $this->service->getCount($dislikeableType, $dislikeableId, 'dislikes'));

        $this->service->persistDeleteDislike($dislikeableType, $dislikeableId, $user->id);

        $this->assertDatabaseMissing('dislikes', [
            'user_id'          => $user->id,
            'dislikeable_id'   => $dislikeableId,
            'dislikeable_type' => $dislikeableType,
        ]);
        $this->assertDatabaseHas('counts', [
            'countable_id'   => $dislikeableId,
            'countable_type' => $dislikeableType,
            'filter'         => 'dislikes',
            'count'          => 0,
        ]);
    }

    public function test_can_cache_and_cache_delete_dislike(): void
    {
        $user            = User::factory()->create();
        $dislikeableId   = $user->id;
        $dislikeableType = User::class;

        $this->service->cacheDislike($dislikeableType, $dislikeableId, $user->id);

        $this->assertTrue($this->service->checkDisliked($dislikeableType, $dislikeableId, $user->id));
        $this->assertEquals(1, $this->service->getDislikesCount($dislikeableType, $dislikeableId));

        $this->service->cacheDeleteDislike($dislikeableType, $dislikeableId, $user->id);

        $this->assertFalse($this->service->checkDisliked($dislikeableType, $dislikeableId, $user->id));
        $this->assertEquals(0, $this->service->getDislikesCount($dislikeableType, $dislikeableId));
    }

    public function test_can_persist_and_persist_delete_save(): void
    {
        $user         = User::factory()->create();
        $saveableId   = $user->id;
        $saveableType = User::class;

        $this->service->persistSave($saveableType, $saveableId, $user->id);

        $this->assertDatabaseHas('saves', [
            'user_id'       => $user->id,
            'saveable_id'   => $saveableId,
            'saveable_type' => $saveableType,
        ]);

        $this->service->persistDeleteSave($saveableType, $saveableId, $user->id);

        $this->assertDatabaseMissing('saves', [
            'user_id'       => $user->id,
            'saveable_id'   => $saveableId,
            'saveable_type' => $saveableType,
        ]);
    }

    public function test_can_cache_and_cache_delete_save(): void
    {
        $user         = User::factory()->create();
        $saveableId   = $user->id;
        $saveableType = User::class;

        $this->service->cacheSave($saveableType, $saveableId, $user->id);

        $this->assertTrue($this->service->checkSaved($saveableType, $saveableId, $user->id));

        $this->service->cacheDeleteSave($saveableType, $saveableId, $user->id);

        $this->assertFalse($this->service->checkSaved($saveableType, $saveableId, $user->id));
    }

    public function test_can_insert_and_retrieve_comments(): void
    {
        $user            = User::factory()->create();
        $commentableId   = $user->id;
        $commentableType = User::class;
        $content         = 'Test comment';

        $commentData = $this->service->insertComment($commentableType, $commentableId, $content, $user->id);

        $this->assertNotNull($commentData->id);
        $this->assertEquals($content, $commentData->content);
        $this->assertEquals(1, $this->service->getCommentsCount($commentableType, $commentableId));

        $retrievedComment = $this->service->getComment($commentData->id);
        $this->assertEquals($commentData->content, $retrievedComment->content);

        $comments = $this->service->getComments($commentableType, $commentableId, 1, 10);
        $this->assertCount(1, $comments);
    }

    public function test_can_insert_reply_comment(): void
    {
         $user           = User::factory()->create();
        $commentableId   = $user->id;
        $commentableType = User::class;
        $content         = 'Parent comment';

        $parentComment = $this->service->insertComment($commentableType, $commentableId, $content, $user->id);

        $replyContent = 'Reply comment';
        $replyComment = $this->service->insertComment($commentableType, $commentableId, $replyContent, $user->id, $parentComment->id);

        $this->assertEquals($parentComment->id, $replyComment->parent);
        $this->assertEquals(1, $this->service->getCommentRepliesCount($commentableType, $commentableId, $parentComment->id));
    }

    public function test_can_cache_and_cache_unfollow_follow(): void
    {
        $user           = User::factory()->create();
        $followableId   = $user->id;
        $followableType = User::class;

        $this->service->cacheFollow($followableType, $followableId, $user->id);

        $this->assertTrue($this->service->checkFollowed($followableType, $followableId, $user->id));
        $this->assertEquals(1, $this->service->getFollowersCount($followableType, $followableId));

        $this->service->cacheDeleteFollow($followableType, $followableId, $user->id);

        $this->assertFalse($this->service->checkFollowed($followableType, $followableId, $user->id));
        $this->assertEquals(0, $this->service->getFollowersCount($followableType, $followableId));
    }

    public function test_can_persist_and_persist_delete_follow(): void
    {
        $user            = User::factory()->create();
        $followableId    = $user->id;
        $followableType  = User::class;

        $this->service->persistFollow($followableType, $followableId, $user->id);

        $this->assertDatabaseHas('follows', [
            'follower_id'          => $user->id,
            'followable_id'        => $followableId,
            'followable_type'      => $followableType,
        ]);
        $this->assertEquals(1, $this->service->getCount($followableType, $followableId, 'followers'));

        $this->service->persistDeleteFollow($followableType, $followableId, $user->id);

        $this->assertDatabaseMissing('follows', [
            'follower_id'          => $user->id,
            'followable_id'        => $followableId,
            'followable_type'      => $followableType,
        ]);
        $this->assertDatabaseHas('counts', [
            'countable_id'   => $followableId,
            'countable_type' => $followableType,
            'filter'         => 'followers',
            'count'          => 0,
        ]);
    }

    public function test_can_increment_and_decrement_count(): void
    {
        $countableId   = 1;
        $countableType = User::class;
        $filter        = 'views';

        $this->service->incrementCount($countableType, $countableId, $filter);
        $this->assertEquals(1, $this->service->getCount($countableType, $countableId, $filter));

        $this->service->incrementCount($countableType, $countableId, $filter, 2);
        $this->assertEquals(3, $this->service->getCount($countableType, $countableId, $filter));

        $this->service->decrementCount($countableType, $countableId, $filter);
        $this->assertEquals(2, $this->service->getCount($countableType, $countableId, $filter));
    }
}
