<?php

namespace Modules\React\Tests\Feature\Listeners;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Article\Models\Article;
use Modules\React\Events\UserDislikedEvent;
use Modules\React\Events\UserFollowedEvent;
use Modules\React\Events\UserLikedEvent;
use Modules\React\Events\UserUndislikedEvent;
use Modules\React\Events\UserUnfollowedEvent;
use Modules\React\Events\UserUnlikedEvent;
use Modules\React\Listeners\SyncDbListener;
use Modules\React\Models\Dislike;
use Modules\React\Models\Follow;
use Modules\React\Models\Like;
use Modules\React\Services\ReactService;
use Tests\TestCase;

class SyncDbListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_handles_user_liked_event_and_persists_like(): void
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

    public function test_it_handles_user_unliked_event_and_removes_like(): void
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

    public function test_it_handles_user_disliked_event_and_persists_dislike(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create();

        Like::create([
            'user_id'       => $user->id,
            'likeable_id'   => $article->id,
            'likeable_type' => Article::class,
        ]);
        app(ReactService::class)->incrementCountDb(Article::class, $article->id, 'likes');

        $event = new UserDislikedEvent($user->id, Article::class, $article->id);

        $listener     = new SyncDbListener();
        $reactService = app(ReactService::class);

        $listener->handleUserDisliked($event, $reactService);

        $this->assertDatabaseHas('dislikes', [
            'user_id'          => $user->id,
            'dislikeable_id'   => $article->id,
            'dislikeable_type' => Article::class,
        ]);

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

        $this->assertDatabaseHas('counts', [
            'countable_id'   => $article->id,
            'countable_type' => Article::class,
            'filter'         => 'dislikes',
            'count'          => 1,
        ]);
    }

    public function test_it_handles_user_undisliked_event_and_removes_dislike(): void
    {
        $user    = User::factory()->create();
        $article = Article::factory()->create();

        Dislike::create([
            'user_id'          => $user->id,
            'dislikeable_id'   => $article->id,
            'dislikeable_type' => Article::class,
        ]);

        app(ReactService::class)->incrementCountDb(Article::class, $article->id, 'dislikes');

        $event = new UserUndislikedEvent($user->id, Article::class, $article->id);

        $listener     = new SyncDbListener();
        $reactService = app(ReactService::class);

        $listener->handleUserUndisliked($event, $reactService);

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
    }

    public function test_it_handles_user_followed_event_and_persists_follow(): void
    {
        $follower = User::factory()->create();
        $target   = User::factory()->create();

        $event = new UserFollowedEvent($follower->id, User::class, $target->id);

        $listener     = new SyncDbListener();
        $reactService = app(ReactService::class);

        $listener->handleUserFollowed($event, $reactService);

        $this->assertDatabaseHas('follows', [
            'follower_id'     => $follower->id,
            'followable_id'   => $target->id,
            'followable_type' => User::class,
        ]);

        $this->assertDatabaseHas('counts', [
            'countable_id'   => $target->id,
            'countable_type' => User::class,
            'filter'         => 'followers',
            'count'          => 1,
        ]);

        $this->assertDatabaseHas('counts', [
            'countable_id'   => $follower->id,
            'countable_type' => User::class,
            'filter'         => 'followings',
            'count'          => 1,
        ]);
    }

    public function test_it_handles_user_unfollowed_event_and_removes_follow(): void
    {
        $follower = User::factory()->create();
        $target   = User::factory()->create();

        Follow::create([
            'follower_id'     => $follower->id,
            'followable_id'   => $target->id,
            'followable_type' => User::class,
        ]);
        app(ReactService::class)->incrementCountDb(User::class, $target->id, 'followers');
        app(ReactService::class)->incrementCountDb(User::class, $follower->id, 'followings');

        $event = new UserUnfollowedEvent($follower->id, User::class, $target->id);

        $listener     = new SyncDbListener();
        $reactService = app(ReactService::class);

        $listener->handleUserUnfollowed($event, $reactService);

        $this->assertDatabaseMissing('follows', [
            'follower_id'     => $follower->id,
            'followable_id'   => $target->id,
            'followable_type' => User::class,
        ]);

        $this->assertDatabaseHas('counts', [
            'countable_id'   => $target->id,
            'countable_type' => User::class,
            'filter'         => 'followers',
            'count'          => 0,
        ]);

        $this->assertDatabaseHas('counts', [
            'countable_id'   => $follower->id,
            'countable_type' => User::class,
            'filter'         => 'followings',
            'count'          => 0,
        ]);
    }
}
