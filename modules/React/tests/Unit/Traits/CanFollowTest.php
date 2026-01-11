<?php

namespace Modules\React\Tests\Unit\Traits;

use App\Models\User;
use Tests\TestCase;

class CanFollowTest extends TestCase
{
    public function test_can_follow_and_unfollow_target(): void
    {
        $follower = User::factory()->create();
        $target   = User::factory()->create();

        $follower->follow($target);

        $this->assertTrue($follower->isFollowing($target));
        $this->assertEquals(1, $follower->followingsCount());

        $follower->unfollow($target);

        $this->assertFalse($follower->isFollowing($target));
        $this->assertEquals(0, $follower->followingsCount());
    }
}
