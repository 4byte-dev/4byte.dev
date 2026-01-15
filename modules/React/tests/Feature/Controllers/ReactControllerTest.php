<?php

namespace Modules\React\Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Modules\React\Events\UserDislikedEvent;
use Modules\React\Events\UserLikedEvent;
use Modules\React\Events\UserSavedEvent;
use Modules\React\Events\UserUnlikedEvent;
use Modules\React\Events\UserUnsavedEvent;
use Modules\React\Services\ReactService;
use Modules\User\Models\User;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ReactControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $gorseMock = \Mockery::mock(\Modules\Recommend\Services\GorseService::class);
        $gorseMock->shouldReceive('insertFeedback')->andReturnNull();
        $gorseMock->shouldReceive('deleteFeedback')->andReturnNull();
        $gorseMock->shouldReceive('getUser')->andReturn(
            \Mockery::mock(\Modules\Recommend\Classes\GorseUser::class)->shouldReceive('addLabel', 'removeLabel')->getMock()
        );
        $gorseMock->shouldReceive('updateUser')->andReturnNull();

        $this->app->instance(\Modules\Recommend\Services\GorseService::class, $gorseMock);

        ReactService::registerHandler('user', User::class, function ($slug) {
            return User::where('username', $slug)->firstOrFail()->id;
        });
    }

    public function test_can_like_a_resource(): void
    {
        Event::fake();

        $user       = User::factory()->create();
        $permission = Permission::firstOrCreate(['name' => 'create_like']);
        $user->givePermissionTo($permission);

        $target = User::factory()->create(['username' => 'target-user']);

        $response = $this->actingAs($user)
            ->postJson(route('api.react.like', ['type' => 'user', 'slug' => 'target-user']));

        $response->assertStatus(200);

        Event::assertDispatched(UserLikedEvent::class, function ($event) use ($user, $target) {
            return $event->userId === $user->id
                && $event->likeableId === $target->id
                && $event->likeableType === User::class;
        });
    }

    public function test_can_unlike_a_resource(): void
    {
        Event::fake();

        $user       = User::factory()->create();
        $permission = Permission::firstOrCreate(['name' => 'create_like']);
        $user->givePermissionTo($permission);

        $target = User::factory()->create(['username' => 'target-user']);

        app(ReactService::class)->cacheLike(User::class, $target->id, $user->id);

        $response = $this->actingAs($user)
            ->postJson(route('api.react.like', ['type' => 'user', 'slug' => 'target-user']));

        $response->assertStatus(200);

        Event::assertDispatched(UserUnlikedEvent::class, function ($event) use ($user, $target) {
            return $event->userId === $user->id
                && $event->likeableId === $target->id
                && $event->likeableType === User::class;
        });
    }

    public function test_can_dislike_a_resource(): void
    {
        Event::fake();

        $user       = User::factory()->create();
        $permission = Permission::firstOrCreate(['name' => 'create_dislike']);
        $user->givePermissionTo($permission);

        $target = User::factory()->create(['username' => 'target-user']);

        $response = $this->actingAs($user)
            ->postJson(route('api.react.dislike', ['type' => 'user', 'slug' => 'target-user']));

        $response->assertStatus(200);

        Event::assertDispatched(UserDislikedEvent::class, function ($event) use ($user, $target) {
            return $event->userId === $user->id
                && $event->dislikeableId === $target->id
                && $event->dislikeableType === User::class;
        });
    }

    public function test_can_save_a_resource(): void
    {
        Event::fake();

        $user       = User::factory()->create();
        $permission = Permission::firstOrCreate(['name' => 'create_save']);
        $user->givePermissionTo($permission);

        $target = User::factory()->create(['username' => 'target-user']);

        $response = $this->actingAs($user)
            ->postJson(route('api.react.save', ['type' => 'user', 'slug' => 'target-user']));

        $response->assertStatus(200);

        Event::assertDispatched(UserSavedEvent::class, function ($event) use ($user, $target) {
            return $event->userId === $user->id
                && $event->saveableId === $target->id
                && $event->saveableType === User::class;
        });
    }

    public function test_can_unsave_a_resource(): void
    {
        Event::fake();

        $user       = User::factory()->create();
        $permission = Permission::firstOrCreate(['name' => 'create_save']);
        $user->givePermissionTo($permission);

        $target = User::factory()->create(['username' => 'target-user']);

        app(ReactService::class)->cacheSave(User::class, $target->id, $user->id);

        $response = $this->actingAs($user)
            ->postJson(route('api.react.save', ['type' => 'user', 'slug' => 'target-user']));

        $response->assertStatus(200);

        Event::assertDispatched(UserUnsavedEvent::class, function ($event) use ($user, $target) {
            return $event->userId === $user->id
                && $event->saveableId === $target->id
                && $event->saveableType === User::class;
        });
    }

    public function test_can_follow_a_resource(): void
    {
        $user       = User::factory()->create();
        $permission = Permission::firstOrCreate(['name' => 'create_follow']);
        $user->givePermissionTo($permission);

        $target = User::factory()->create(['username' => 'target-user']);

        $response = $this->actingAs($user)
            ->postJson(route('api.react.follow', ['type' => 'user', 'slug' => 'target-user']));

        $response->assertStatus(200);
        $this->assertTrue($target->isFollowedBy($user->id));
    }

    public function test_can_comment_on_a_resource(): void
    {
        $user       = User::factory()->create();
        $permission = Permission::firstOrCreate(['name' => 'create_comment']);
        $user->givePermissionTo($permission);

        User::factory()->create(['username' => 'target-user']);

        $response = $this->actingAs($user)
            ->postJson(route('api.react.comment', ['type' => 'user', 'slug' => 'target-user']), [
                'content' => 'This is a test comment that is long enough.',
            ]);

        $response->assertStatus(200);
    }

    public function test_can_get_comments_list(): void
    {
        $user   = User::factory()->create();
        $target = User::factory()->create(['username' => 'target-user']);

        $service = new ReactService();
        $service->insertComment(User::class, $target->id, 'Test comment content', $user->id);

        $response = $this->actingAs($user)->postJson(route('api.react.comments', ['type' => 'user', 'slug' => 'target-user']));

        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }
}
