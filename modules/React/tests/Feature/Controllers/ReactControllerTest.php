<?php

namespace Modules\React\Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\React\Services\ReactService;
use Modules\User\Models\User;
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
        $user       = User::factory()->create();
        $permission = \Spatie\Permission\Models\Permission::create(['name' => 'create_like']);
        $user->givePermissionTo($permission);

        $target = User::factory()->create(['username' => 'target-user']);

        $response = $this->actingAs($user)
            ->postJson(route('api.react.like', ['type' => 'user', 'slug' => 'target-user']));

        $response->assertStatus(200);

        $this->assertTrue(\Modules\React\Models\Like::where([
            'user_id'       => $user->id,
            'likeable_id'   => $target->id,
            'likeable_type' => User::class,
        ])->exists());
    }

    public function test_can_dislike_a_resource(): void
    {
        $user       = User::factory()->create();
        $permission = \Spatie\Permission\Models\Permission::create(['name' => 'create_dislike']);
        $user->givePermissionTo($permission);

        $target = User::factory()->create(['username' => 'target-user']);

        $response = $this->actingAs($user)
            ->postJson(route('api.react.dislike', ['type' => 'user', 'slug' => 'target-user']));

        $response->assertStatus(200);
        $this->assertTrue(\Modules\React\Models\Dislike::where([
            'user_id'          => $user->id,
            'dislikeable_id'   => $target->id,
            'dislikeable_type' => User::class,
        ])->exists());
    }

    public function test_can_save_a_resource(): void
    {
        $user       = User::factory()->create();
        $permission = \Spatie\Permission\Models\Permission::create(['name' => 'create_save']);
        $user->givePermissionTo($permission);

        $target = User::factory()->create(['username' => 'target-user']);

        $response = $this->actingAs($user)
            ->postJson(route('api.react.save', ['type' => 'user', 'slug' => 'target-user']));

        $response->assertStatus(200);
        $this->assertTrue(\Modules\React\Models\Save::where([
            'user_id'       => $user->id,
            'saveable_id'   => $target->id,
            'saveable_type' => User::class,
        ])->exists());
    }

    public function test_can_follow_a_resource(): void
    {
        $user       = User::factory()->create();
        $permission = \Spatie\Permission\Models\Permission::create(['name' => 'create_follow']);
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
        $permission = \Spatie\Permission\Models\Permission::create(['name' => 'create_comment']);
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
