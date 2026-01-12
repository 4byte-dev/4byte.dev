<?php

namespace Modules\React\Tests\Feature\Listeners;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Article\Models\Article;
use Modules\React\Events\UserLikedEvent;
use Modules\React\Events\UserUnlikedEvent;
use Modules\React\Listeners\SyncDbListener;
use Modules\React\Models\Dislike;
use Modules\React\Models\Like;
use Modules\React\Services\ReactService;
use Tests\TestCase;

class SyncDbListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_handles_user_liked_event_and_persists_like()
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create();

        Dislike::create([
            'user_id'          => $user->id,
            'dislikeable_id'   => $article->id,
            'dislikeable_type' => Article::class,
        ]);
        app(ReactService::class)->incrementCountDb(Article::class, $article->id, 'dislikes');

        $event = new UserLikedEvent($user->id, Article::class, $article->id);

        $listener     = new SyncDbListener();
        $reactService = app(ReactService::class);

        $listener->handleUserLiked($event, $reactService);

        $this->assertDatabaseHas('likes', [
            'user_id'       => $user->id,
            'likeable_id'   => $article->id,
            'likeable_type' => Article::class,
        ]);

        $this->assertDatabaseMissing('dislikes', [
            'user_id'          => $user->id,
            'dislikeable_id'   => $article->id,
            'dislikeable_type' => Article::class,
        ]);

        $this->assertDatabaseHas('counts', [
            'countable_id'   => $article->id,
            'countable_type' => Article::class,
            'filter'         => 'dislikes',
            'count'          => 0,
        ]);

        $this->assertDatabaseHas('counts', [
            'countable_id'   => $article->id,
            'countable_type' => Article::class,
            'filter'         => 'likes',
            'count'          => 1,
        ]);
    }

    public function test_it_handles_user_unliked_event_and_removes_like()
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create();

        Like::create([
            'user_id'       => $user->id,
            'likeable_id'   => $article->id,
            'likeable_type' => Article::class,
        ]);

        app(ReactService::class)->incrementCountDb(Article::class, $article->id, 'likes');

        $event = new UserUnlikedEvent($user->id, Article::class, $article->id);

        $listener     = new SyncDbListener();
        $reactService = app(ReactService::class);

        $listener->handleUserUnliked($event, $reactService);

        $this->assertDatabaseMissing('likes', [
            'user_id'       => $user->id,
            'likeable_id'   => $article->id,
            'likeable_type' => Article::class,
        ]);

         $this->assertDatabaseHas('counts', [
            'countable_id'   => $article->id,
            'countable_type' => Article::class,
            'filter'         => 'likes',
            'count'          => 0,
        ]);
    }
}
