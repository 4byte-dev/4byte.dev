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

    public function test_can_insert_and_delete_like(): void
    {
        $user         = User::factory()->create();
        $likeableId   = $user->id;
        $likeableType = User::class;

        $this->service->insertLike($likeableType, $likeableId, $user->id);

        $this->assertTrue($this->service->checkLiked($likeableType, $likeableId, $user->id));
        $this->assertEquals(1, $this->service->getLikesCount($likeableType, $likeableId));

        $this->service->deleteLike($likeableType, $likeableId, $user->id);

        $this->assertFalse($this->service->checkLiked($likeableType, $likeableId, $user->id));
        $this->assertEquals(0, $this->service->getLikesCount($likeableType, $likeableId));
    }

    public function test_can_insert_and_delete_dislike(): void
    {
        $user            = User::factory()->create();
        $dislikeableId   = $user->id;
        $dislikeableType = User::class;

        $this->service->insertDislike($dislikeableType, $dislikeableId, $user->id);

        $this->assertTrue($this->service->checkDisliked($dislikeableType, $dislikeableId, $user->id));
        $this->assertEquals(1, $this->service->getDislikesCount($dislikeableType, $dislikeableId));

        $this->service->deleteDislike($dislikeableType, $dislikeableId, $user->id);

        $this->assertFalse($this->service->checkDisliked($dislikeableType, $dislikeableId, $user->id));
        $this->assertEquals(0, $this->service->getDislikesCount($dislikeableType, $dislikeableId));
    }

    public function test_can_insert_and_delete_save(): void
    {
        $user         = User::factory()->create();
        $saveableId   = $user->id;
        $saveableType = User::class;

        $this->service->insertSave($saveableType, $saveableId, $user->id);

        $this->assertTrue($this->service->checkSaved($saveableType, $saveableId, $user->id));

        $this->service->deleteSave($saveableType, $saveableId, $user->id);

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

    public function test_can_insert_and_delete_follow(): void
    {
        $follower       = User::factory()->create();
        $followableId   = $follower->id;
        $followableType = User::class;

        $target       = User::factory()->create();
        $followableId = $target->id;

        $this->service->insertFollow($followableType, $followableId, $follower->id);

        $this->assertTrue($this->service->checkFollowed($followableType, $followableId, $follower->id));
        $this->assertEquals(1, $this->service->getFollowersCount($followableType, $followableId));
        $this->assertEquals(1, $this->service->getFollowingsCount($follower->id));

        $this->service->deleteFollow($followableType, $followableId, $follower->id);

        $this->assertFalse($this->service->checkFollowed($followableType, $followableId, $follower->id));
        $this->assertEquals(0, $this->service->getFollowersCount($followableType, $followableId));
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
