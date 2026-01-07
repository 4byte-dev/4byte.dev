<?php

namespace Modules\Recommend\Tests\Unit\Services;

use Illuminate\Support\Facades\Http;
use Modules\Recommend\Classes\GorseFeedback;
use Modules\Recommend\Classes\GorseItem;
use Modules\Recommend\Classes\GorseUser;
use Modules\Recommend\Services\GorseService;
use Modules\Recommend\Tests\TestCase;

class GorseServiceTest extends TestCase
{
    private GorseService $service;

    protected function setUp(): void
    {
        parent::setUp();

        config(['recommend.endpoint' => 'http://gorse:8088']);
        config(['recommend.apiKey' => 'test-api-key']);

        $this->service = new GorseService();
    }

    public function test_insert_user(): void
    {
        Http::fake([
            'http://gorse:8088/api/user' => Http::response(['RowAffected' => 1]),
        ]);

        $user   = new GorseUser('user1', [], [], null);
        $result = $this->service->insertUser($user);

        $this->assertNotNull($result);
        $this->assertEquals(1, $result->getRowAffected());
    }

    public function test_get_user(): void
    {
        Http::fake([
            'http://gorse:8088/api/user/user1' => Http::response([
                'UserId'    => 'user1',
                'Labels'    => ['a', 'b'],
                'Subscribe' => [],
                'Comment'   => '',
            ]),
        ]);

        $user = $this->service->getUser('user1');

        $this->assertNotNull($user);
        $this->assertEquals('user1', $user->getUserId());
        $this->assertEquals(['a', 'b'], $user->getLabels());
    }

    public function test_insert_item(): void
    {
        Http::fake([
            'http://gorse:8088/api/item' => Http::response(['RowAffected' => 1]),
        ]);

        $item   = new GorseItem('item1', [], [], '', false, now()->toDateTimeString());
        $result = $this->service->insertItem($item);

        $this->assertNotNull($result);
        $this->assertEquals(1, $result->getRowAffected());
    }

    public function test_get_recommend(): void
    {
        Http::fake([
            'http://gorse:8088/api/recommend/user1?*' => Http::response(['item1', 'item2']),
        ]);

        $items = $this->service->getRecommend('user1', 10, 0);

        $this->assertIsArray($items);
        $this->assertCount(2, $items);
        $this->assertEquals('item1', $items[0]);
    }

    public function test_insert_feedback(): void
    {
        Http::fake([
            'http://gorse:8088/api/feedback' => Http::response(['RowAffected' => 1]),
        ]);

        $feedback = new GorseFeedback('read', 'user1', 'item1', '', now()->toDateTimeString());
        $result   = $this->service->insertFeedback($feedback);

        $this->assertNotNull($result);
        $this->assertEquals(1, $result->getRowAffected());
    }
}
