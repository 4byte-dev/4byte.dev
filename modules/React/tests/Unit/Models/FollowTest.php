<?php

namespace Modules\React\Tests\Unit\Models;

use Modules\React\Models\Follow;
use Modules\React\Tests\TestCase;

class FollowTest extends TestCase
{
    public function test_can_instantiate_follow_model(): void
    {
        $target = \Modules\User\Models\User::factory()->create();
        $follow = Follow::factory()->create([
            'followable_id'   => $target->id,
            'followable_type' => \Modules\User\Models\User::class,
        ]);
        $this->assertInstanceOf(Follow::class, $follow);
    }
}
