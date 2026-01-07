<?php

namespace Modules\Recommend\Tests\Unit\Classes;

use Modules\Recommend\Classes\GorseItem;
use Modules\Recommend\Tests\TestCase;

class GorseItemTest extends TestCase
{
    public function test_serialization(): void
    {
        $timestamp = now()->toDateTimeString();

        $item = new GorseItem(
            'item1',
            ['label1'],
            ['cat1'],
            'comment',
            false,
            $timestamp
        );

        $decoded = json_decode(json_encode($item), true);

        $this->assertSame('item1', $decoded['ItemId']);
        $this->assertSame(['label1'], $decoded['Labels']);
        $this->assertSame(['cat1'], $decoded['Categories']);
        $this->assertFalse($decoded['IsHidden']);
        $this->assertSame($timestamp, $decoded['Timestamp']);
    }

    public function test_from_json_sets_default_timestamp(): void
    {
        $item = GorseItem::fromJSON([
            'ItemId'     => 'item1',
            'Labels'     => [],
            'Categories' => [],
            'Comment'    => 'comment',
            'IsHidden'   => true,
        ]);

        $this->assertNotEmpty($item->getTimestamp());
    }
}
