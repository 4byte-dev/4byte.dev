<?php

namespace Modules\React\Tests\Unit\Models;

use Modules\React\Models\Dislike;
use Modules\React\Tests\TestCase;

class DislikeTest extends TestCase
{
    public function test_can_instantiate_dislike_model(): void
    {
        $target  = \Modules\User\Models\User::factory()->create();
        $dislike = Dislike::factory()->create([
            'dislikeable_id'   => $target->id,
            'dislikeable_type' => \Modules\User\Models\User::class,
        ]);
        $this->assertInstanceOf(Dislike::class, $dislike);
    }
}
