<?php

namespace Modules\React\Tests\Unit\Database\Factories;

use Modules\React\Database\Factories\DislikeFactory;
use Modules\React\Models\Dislike;
use Modules\React\Tests\TestCase;

class DislikeFactoryTest extends TestCase
{
    public function test_can_create_dislike_instance(): void
    {
        $factory = DislikeFactory::new();
        $this->assertInstanceOf(DislikeFactory::class, $factory);
    }

    public function test_can_create_dislike(): void
    {
        $target  = \Modules\User\Models\User::factory()->create();
        $dislike = Dislike::factory()->create([
            'dislikeable_id'   => $target->id,
            'dislikeable_type' => \Modules\User\Models\User::class,
        ]);
        $this->assertInstanceOf(Dislike::class, $dislike);
    }
}
