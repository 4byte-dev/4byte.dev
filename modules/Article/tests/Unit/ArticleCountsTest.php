<?php

namespace Modules\Article\Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Modules\Article\Models\Article;
use Modules\React\Events\UserDislikedEvent;
use Modules\React\Events\UserLikedEvent;
use Modules\React\Events\UserUndislikedEvent;
use Modules\React\Events\UserUnlikedEvent;
use Modules\React\Models\Comment;
use Modules\React\Services\ReactService;
use Tests\TestCase;

class ArticleCountsTest extends TestCase
{
    use RefreshDatabase;

    public function test_article_likes_use_count_model(): void
    {
        Event::fake();

        $user    = User::factory()->create();
        $article = Article::factory()->create();

        $this->assertEquals(0, $article->likesCount());
        $this->assertDatabaseMissing('counts', [
            'countable_type' => $article->getMorphClass(),
            'countable_id'   => $article->id,
            'filter'         => 'likes',
        ]);

        $article->like($user->id);

        Event::assertDispatched(UserLikedEvent::class);

        $this->assertEquals(1, $article->likesCount());

        $article->unlike($user->id);

        Event::assertDispatched(UserUnlikedEvent::class);

        $this->assertEquals(0, $article->likesCount());
    }

    public function test_article_dislikes_use_count_model(): void
    {
        Event::fake();

        $user    = User::factory()->create();
        $article = Article::factory()->create();

        $this->assertEquals(0, $article->dislikesCount());

        $article->dislike($user->id);

        Event::assertDispatched(UserDislikedEvent::class);

        $this->assertEquals(1, $article->dislikesCount());

        $article->undislike($user->id);

        Event::assertDispatched(UserUndislikedEvent::class);

        $this->assertEquals(0, $article->dislikesCount());
    }

    public function test_article_comments_use_count_model(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create();

        $comment = app(ReactService::class)->insertComment($article->getMorphClass(), $article->id, 'Test Comment Content', $user->id);

        $this->assertDatabaseHas('counts', [
            'countable_type' => $article->getMorphClass(),
            'countable_id'   => $article->id,
            'filter'         => 'comments',
            'count'          => 1,
        ]);

        $this->assertEquals(1, $article->commentsCount());

        app(ReactService::class)->insertComment($article->getMorphClass(), $article->id, 'Test Reply Content', $user->id, $comment->id);

        $this->assertDatabaseHas('counts', [
            'countable_type' => $article->getMorphClass(),
            'countable_id'   => $article->id,
            'filter'         => 'comments',
            'count'          => 2,
        ]);
        $this->assertEquals(2, $article->commentsCount());

        $this->assertDatabaseHas('counts', [
            'countable_type' => Comment::class,
            'countable_id'   => $comment->id,
            'filter'         => 'replies',
            'count'          => 1,
        ]);

        $commentModel = Comment::find($comment->id);
        $this->assertEquals(1, $commentModel->repliesCount());
    }
}
