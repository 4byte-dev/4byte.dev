<?php

namespace Modules\React\Tests\Unit\Events;

use Modules\React\Events\FollowedEvent;
use Modules\React\Models\Follow;
use Tests\TestCase;

class FollowedEventTest extends TestCase
{
    public function test_event_contains_follow_model(): void
    {
        $follower = \Modules\User\Models\User::factory()->create();
        $target   = \Modules\User\Models\User::factory()->create();

        $follow = Follow::factory()->create([
            'follower_id'     => $follower->id,
            'followable_id'   => $target->id,
            'followable_type' => \Modules\User\Models\User::class,
        ]);
        $event = new FollowedEvent($follow);

        $this->assertEquals($follow->follower_id, $event->follow['follower_id']);
    }
}
