<?php

namespace Modules\React\Tests\Unit\Database\Factories;

use Modules\React\Database\Factories\LikeFactory;
use Modules\React\Models\Like;
use Modules\React\Tests\TestCase;

class LikeFactoryTest extends TestCase
{
    public function test_can_create_like_instance(): void
    {
        $factory = LikeFactory::new();
        $this->assertInstanceOf(LikeFactory::class, $factory);
    }

    public function test_can_create_like(): void
    {
        $target = \Modules\User\Models\User::factory()->create();
        $like   = Like::factory()->create([
            'likeable_id'   => $target->id,
            'likeable_type' => \Modules\User\Models\User::class,
        ]);
        $this->assertInstanceOf(Like::class, $like);
    }
}
