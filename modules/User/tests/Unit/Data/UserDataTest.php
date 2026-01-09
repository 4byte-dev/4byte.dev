<?php

namespace Modules\User\Tests\Unit\Data;

use App\Models\User as AppUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Mockery\MockInterface;
use Modules\User\Data\UserData;
use Modules\User\Models\User;
use Modules\User\Tests\TestCase;

class UserDataTest extends TestCase
{
    public function test_it_can_be_instantiated_with_defaults(): void
    {
        $createdAt = now();

        $userData = new UserData(
            id: 10,
            name: 'Test User',
            username: 'testuser',
            avatar: 'https://cdn.4byte.dev/avatar.png',
            followers: 15,
            followings: 7,
            isFollowing: true,
            created_at: $createdAt
        );

        $this->assertSame(10, $userData->id);
        $this->assertSame('Test User', $userData->name);
        $this->assertSame('testuser', $userData->username);
        $this->assertSame('https://cdn.4byte.dev/avatar.png', $userData->avatar);

        $this->assertSame(15, $userData->followers);
        $this->assertSame(7, $userData->followings);
        $this->assertTrue($userData->isFollowing);

        $this->assertInstanceOf(Carbon::class, $userData->created_at);
        $this->assertSame('user', $userData->type);
    }

    public function test_it_creates_data_from_model_without_id_by_default(): void
    {
        $user = User::factory()->create([
            'name'     => 'John Doe',
            'username' => 'johndoe',
        ]);

        $data = UserData::fromModel($user);

        $this->assertSame(0, $data->id);
        $this->assertSame('John Doe', $data->name);
        $this->assertSame('johndoe', $data->username);

        $this->assertSame($user->created_at->toISOString(), $data->created_at->toISOString());
    }

    public function test_it_sets_id_when_flag_is_true(): void
    {
        $user = User::factory()->create();

        $data = UserData::fromModel($user, true);

        $this->assertSame($user->id, $data->id);
    }

    public function test_it_uses_model_methods_for_follow_counts_and_follow_state(): void
    {
        $authUser = AppUser::factory()->create();
        $this->actingAs($authUser);

        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();

        $user->id         = 5;
        $user->name       = 'Mock User';
        $user->username   = 'mockuser';
        $user->created_at = now();

        $user->shouldReceive('getAvatarImage')
            ->once()
            ->andReturn('avatar.png');

        $user->shouldReceive('followersCount')
            ->once()
            ->andReturn(20);

        $user->shouldReceive('followingsCount')
            ->once()
            ->andReturn(8);

        $user->shouldReceive('isFollowedBy')
            ->once()
            ->with($authUser->id)
            ->andReturn(true);

        $data = UserData::fromModel($user, true);

        $this->assertSame(5, $data->id);
        $this->assertSame('Mock User', $data->name);
        $this->assertSame('mockuser', $data->username);
        $this->assertSame('avatar.png', $data->avatar);

        $this->assertSame(20, $data->followers);
        $this->assertSame(8, $data->followings);
        $this->assertTrue($data->isFollowing);
    }

    public function test_it_sets_is_following_false_for_guest_user(): void
    {
        Auth::logout();

        /** @var User|MockInterface $user */
        $user = Mockery::mock(User::class)->makePartial();

        $user->id         = 3;
        $user->name       = 'Guest Target';
        $user->username   = 'guesttarget';
        $user->created_at = now();

        $user->shouldReceive('getAvatarImage')
            ->once()
            ->andReturn("cdn.4byte.dev/avatar_guest.png");

        $user->shouldReceive('followersCount')
            ->once()
            ->andReturn(0);

        $user->shouldReceive('followingsCount')
            ->once()
            ->andReturn(0);

        $user->shouldReceive('isFollowedBy')
            ->once()
            ->with(null)
            ->andReturn(false);

        $data = UserData::fromModel($user);

        $this->assertFalse($data->isFollowing);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
