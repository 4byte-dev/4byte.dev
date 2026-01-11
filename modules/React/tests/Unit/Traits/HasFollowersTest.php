<?php

namespace Modules\React\Tests\Unit\Traits;

use App\Models\User;
use Tests\TestCase;

class HasFollowersTest extends TestCase
{
    public function test_can_check_followers_count_and_status(): void
    {
        $user     = User::factory()->create();
        $follower = User::factory()->create();

        $user->followers()->create([
            'follower_id' => $follower->id,
        ]);

        $this->assertTrue($user->isFollowedBy($follower->id));
        $this->assertEquals(1, $user->followersCount());
    }
}
