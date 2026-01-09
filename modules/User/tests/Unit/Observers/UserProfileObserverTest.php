<?php

namespace Modules\User\Tests\Unit\Observers;

use Illuminate\Support\Facades\Cache;
use Modules\User\Models\UserProfile;
use Modules\User\Observers\UserProfileObserver;
use Modules\User\Tests\TestCase;

class UserProfileObserverTest extends TestCase
{
    private UserProfileObserver $observer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->observer = new UserProfileObserver();
    }

    public function test_updated_clears_user_profile_cache(): void
    {
        // Arrange
        $profile = UserProfile::factory()->make([
            'user_id' => 1,
        ]);

        Cache::shouldReceive('forget')
            ->once()
            ->with('user:1:profile');

        // Act
        $this->observer->updated($profile);
    }

    public function test_deleted_clears_user_profile_cache(): void
    {
        // Arrange
        $profile = UserProfile::factory()->make([
            'user_id' => 1,
        ]);

        Cache::shouldReceive('forget')
            ->once()
            ->with('user:1:profile');

        // Act
        $this->observer->deleted($profile);
    }
}
