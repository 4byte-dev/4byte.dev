<?php

namespace Modules\React\Tests\Unit\Models;

use Modules\React\Models\Like;
use Modules\React\Tests\TestCase;

class LikeTest extends TestCase
{
    public function test_can_instantiate_like_model(): void
    {
        $target = \Modules\User\Models\User::factory()->create();
        $like   = Like::factory()->create([
            'likeable_id'   => $target->id,
            'likeable_type' => \Modules\User\Models\User::class,
        ]);
        $this->assertInstanceOf(Like::class, $like);
    }
}
