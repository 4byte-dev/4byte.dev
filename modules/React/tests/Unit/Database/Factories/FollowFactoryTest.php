<?php

namespace Modules\React\Tests\Unit\Database\Factories;

use Modules\React\Database\Factories\FollowFactory;
use Modules\React\Models\Follow;
use Modules\React\Tests\TestCase;

class FollowFactoryTest extends TestCase
{
    public function test_can_create_follow_instance(): void
    {
        $factory = FollowFactory::new();
        $this->assertInstanceOf(FollowFactory::class, $factory);
    }

    public function test_can_create_follow(): void
    {
        $target = \Modules\User\Models\User::factory()->create();
        $follow = Follow::factory()->create([
            'followable_id'   => $target->id,
            'followable_type' => \Modules\User\Models\User::class,
        ]);
        $this->assertInstanceOf(Follow::class, $follow);
    }
}
