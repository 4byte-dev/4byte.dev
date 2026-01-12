<?php

namespace Modules\React\Tests\Unit\Events;

use Modules\React\Events\UserUndislikedEvent;
use Tests\TestCase;

class UserUndislikedEventTest extends TestCase
{
    public function test_it_has_correct_properties(): void
    {
        $userId          = 1;
        $dislikeableType = 'post';
        $dislikeableId   = 1;

        $event = new UserUndislikedEvent($userId, $dislikeableType, $dislikeableId);

        $this->assertEquals($userId, $event->userId);
        $this->assertEquals($dislikeableType, $event->dislikeableType);
        $this->assertEquals($dislikeableId, $event->dislikeableId);
    }
}
