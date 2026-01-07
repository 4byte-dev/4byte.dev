<?php

namespace Modules\Recommend\Tests\Unit\Classes;

use Modules\Recommend\Classes\GorseFeedback;
use Modules\Recommend\Tests\TestCase;

class GorseFeedbackTest extends TestCase
{
    public function test_serialization(): void
    {
        $timestamp = now()->toDateTimeString();

        $feedback = new GorseFeedback(
            'click',
            'user1',
            'item1',
            'comment',
            $timestamp
        );

        $decoded = json_decode(json_encode($feedback), true);

        $this->assertSame('click', $decoded['FeedbackType']);
        $this->assertSame('user1', $decoded['UserId']);
        $this->assertSame('item1', $decoded['ItemId']);
        $this->assertSame('comment', $decoded['Comment']);
        $this->assertSame($timestamp, $decoded['Timestamp']);
    }
}
