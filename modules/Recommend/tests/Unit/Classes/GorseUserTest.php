<?php

namespace Modules\Recommend\Tests\Unit\Classes;

use Modules\Recommend\Classes\GorseUser;
use Modules\Recommend\Tests\TestCase;

class GorseUserTest extends TestCase
{
    public function test_serializes_to_expected_json(): void
    {
        $user = new GorseUser(
            'user1',
            ['label1'],
            'comment'
        );

        $decoded = json_decode(json_encode($user), true);

        $this->assertSame('user1', $decoded['UserId']);
        $this->assertSame(['label1'], $decoded['Labels']);
        $this->assertSame('comment', $decoded['Comment']);
    }

    public function test_can_be_hydrated_from_json(): void
    {
        $user = GorseUser::fromJSON([
            'UserId'    => 'user1',
            'Labels'    => ['label1'],
            'Comment'   => 'comment',
        ]);

        $this->assertEquals('user1', $user->getUserId());
        $this->assertEquals(['label1'], $user->getLabels());
        $this->assertEquals('comment', $user->getComment());
    }

    public function test_add_label(): void
    {
        $user = new GorseUser('user1', [], null);

        $user->addLabel('new-label');

        $this->assertContains('new-label', $user->getLabels());
    }

    public function test_remove_label(): void
    {
        $user = new GorseUser('user1', ['label1', 'label2'], null);

        $user->removeLabel('label1');

        $this->assertEquals(['label2'], array_values($user->getLabels()));
    }
}
