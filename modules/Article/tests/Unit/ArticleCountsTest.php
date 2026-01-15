<?php

namespace Modules\Article\Tests\Unit;

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Modules\Article\Models\Article;
use Modules\React\Actions\CommentAction;
use Modules\React\Events\UserDislikedEvent;
use Modules\React\Events\UserLikedEvent;
use Modules\React\Events\UserUndislikedEvent;
use Modules\React\Events\UserUnlikedEvent;
use Modules\React\Models\Comment;
use Modules\React\Services\ReactService;
use Tests\TestCase;

class ArticleCountsTest extends TestCase
{
    protected ReactService $reactService;

    public function setUp(): void
    {
        parent::setUp();

        $this->reactService = app(ReactService::class);
    }

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
}
