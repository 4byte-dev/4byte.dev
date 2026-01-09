<?php

namespace Modules\User\Tests\Unit\Services;

use Illuminate\Support\Facades\Cache;
use Modules\User\Models\User;
use Modules\User\Models\UserProfile;
use Modules\User\Services\UserService;
use Modules\User\Tests\TestCase;

class UserServiceTest extends TestCase
{
    protected UserService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
        $this->service = app(UserService::class);
    }

    public function test_get_data_returns_user_data(): void
    {
        $user = User::factory()->create();

        $data = $this->service->getData($user->id);

        $this->assertEquals($user->name, $data->name);
        $this->assertEquals($user->username, $data->username);

        $this->assertTrue(Cache::has("user:{$user->id}"));
    }

    public function test_it_can_get_user_id_by_username(): void
    {
        $user = User::factory()->create();

        $id = $this->service->getId($user->username);

        $this->assertEquals($user->id, $id);
        $this->assertTrue(Cache::has("user:{$user->username}:id"));
    }

    public function test_get_profile_data_returns_user_profile_data(): void
    {
        $user = User::factory()->create();

        $profile = UserProfile::factory()->create([
            'user_id' => $user->id,
        ]);

        $data = $this->service->getProfileData($user->id);

        $this->assertEquals($profile->role, $data->role);
        $this->assertEquals($profile->bio, $data->bio);
        $this->assertEquals($profile->location, $data->location);

        $this->assertTrue(Cache::has("user:{$user->id}:profile"));
    }
}
