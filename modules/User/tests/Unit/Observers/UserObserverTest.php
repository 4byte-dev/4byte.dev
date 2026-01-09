<?php

namespace Modules\User\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Mockery;
use Mockery\MockInterface;
use Modules\Recommend\Classes\GorseUser;
use Modules\Recommend\Services\GorseService;
use Modules\User\Models\User;
use Modules\User\Observers\UserObserver;
use Modules\User\Tests\TestCase;

class UserObserverTest extends TestCase
{
    private GorseService|MockInterface $gorse;

    private UserObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gorse    = Mockery::mock(GorseService::class);
        $this->observer = new UserObserver($this->gorse);
    }

    public function test_created_creates_profile_and_inserts_user_to_gorse(): void
    {
        $user = User::factory()->create();

        $this->gorse->shouldReceive('insertUser')
            ->once()
            ->with(Mockery::type(GorseUser::class));

        $this->observer->created($user);

        $this->assertDatabaseHas('user_profiles', [
            'user_id' => $user->id,
            'role'    => 'Developer',
        ]);
    }

    public function test_updated_clears_user_cache(): void
    {
        $user = User::factory()->make(['id' => 1]);

        Cache::shouldReceive('forget')
            ->once()
            ->with('user:1');

        $this->observer->updated($user);
    }

    public function test_deleted_removes_user_from_gorse_and_clears_cache(): void
    {
        $user = User::factory()->make([
            'id'       => 1,
            'username' => 'johndoe',
        ]);

        $this->gorse->shouldReceive('deleteUser')
            ->once()
            ->with('1');

        Cache::shouldReceive('forget')->with('user:johndoe:id');
        Cache::shouldReceive('forget')->with('user:1');
        Cache::shouldReceive('forget')->with('user:1:followers');
        Cache::shouldReceive('forget')->with('user:1:followings');

        $this->observer->deleted($user);
    }
}
